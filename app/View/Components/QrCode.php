<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use BaconQrCode\Writer;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\SvgImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;

class QrCode extends Component
{
    public $data;
    public $size;

    /**
     * Create a new component instance.
     */
    public function __construct($data, $size = 200)
    {
        $this->data = $data;
        $this->size = $size;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        $renderer = new ImageRenderer(
            new RendererStyle($this->size),
            new SvgImageBackEnd()
        );
        $writer = new Writer($renderer);
        $svg = $writer->writeString($this->data);

        return view('components.qr-code', ['svg' => $svg]);
    }
}
