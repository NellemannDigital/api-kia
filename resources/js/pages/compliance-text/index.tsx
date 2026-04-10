import { useState, useMemo, useEffect } from 'react';
import { Head } from '@inertiajs/react';
import axios from 'axios';

import AppLayout from '@/layouts/app-layout';
import { Car } from '@/types/Car';

import {
  Select,
  SelectContent,
  SelectGroup,
  SelectItem,
  SelectLabel,
  SelectTrigger,
  SelectValue,
} from "@/components/ui/select"
import { Textarea } from "@/components/ui/textarea";
import { Button } from "@/components/ui/button";
import { X, Check, Copy } from 'lucide-react';

const breadcrumbs = [
  { title: 'Generate compliance text', href: '/compliance-text-generator' }
]

interface Props {
  cars: Car[];
  templates: {
    id: number;
    variant: string;
  }[];
}

export default function Index({ cars, templates }: Props) {

  const [selectedCarId, setSelectedCarId] = useState<number | null>(null);
  const [selectedTrimId, setSelectedTrimId] = useState<number | null>(null);
  const [selectedPowertrainId, setSelectedPowertrainId] = useState<number | null>(null);
  const [selectedTemplateId, setSelectedTemplateId] = useState<number | null>(null);

  const [complianceText, setComplianceText] = useState<string>('');
  const [loadingText, setLoadingText] = useState(false);
  const [copied, setCopied] = useState(false);
  const [copiedEmbed, setCopiedEmbed] = useState(false);

  const selectedCar = useMemo(
    () => cars.find(c => c.id === selectedCarId),
    [cars, selectedCarId]
  );

  const trims = useMemo(
    () => selectedCar?.trims ?? [],
    [selectedCar]
  );

  const selectedTrim = useMemo(
    () => trims.find(t => t.id === selectedTrimId),
    [trims, selectedTrimId]
  );

  const powertrains = useMemo(
    () => selectedTrim?.powertrains ?? [],
    [selectedTrim]
  );

  const handleCarChange = (value: string) => {
    const id = Number(value);

    setSelectedCarId(id);
    setSelectedTrimId(null);
    setSelectedPowertrainId(null);
  };

  const handleTrimChange = (value: string) => {
    const id = Number(value);

    setSelectedTrimId(id);
    setSelectedPowertrainId(null);
  };

  const handlePowertrainChange = (value: string) => {
    setSelectedPowertrainId(Number(value));
  };

  const handleCopy = async () => {
    if (!complianceText) return;

    await navigator.clipboard.writeText(complianceText);

    setCopied(true);

    setTimeout(() => setCopied(false), 1500);
  };

  const handleEmbedCopy = async () => {
    if (!embedCode) return;

    await navigator.clipboard.writeText(embedCode);

    setCopiedEmbed(true);

    setTimeout(() => setCopiedEmbed(false), 1500);
  };

  const embedCode = useMemo(() => {
    if (!selectedCarId || !selectedTemplateId) return "";

    return `<style>
  .kia-compliance {
    --kia-font: Arial, sans-serif;
    --kia-color: #111;
    --kia-font-size: 14px;
    --kia-line-height: 1.6;
  }
</style>

<div
  id="kia-compliance"
  data-car="${selectedCarId}"
  data-trim="${selectedTrimId ?? ""}"
  data-powertrain="${selectedPowertrainId ?? ""}"
  data-template="${selectedTemplateId}"
></div>

<script src="https://api-kia.test/embed/compliance.js"></script>`;
  }, [selectedCarId, selectedTrimId, selectedPowertrainId, selectedTemplateId]);

  useEffect(() => {
    if (!selectedCarId) {
      setComplianceText('');
      return;
    }

    const source = axios.CancelToken.source();

    const timeout = setTimeout(async () => {
      try {
        setLoadingText(true);

        const response = await axios.get('/api/compliance-text', {
          cancelToken: source.token,
          params: {
            car_id: selectedCarId,
            trim_id: selectedTrimId,
            powertrain_id: selectedPowertrainId,
            template: selectedTemplateId,
          }
        });

        setComplianceText(response.data.text);

      } catch (error: any) {
        if (!axios.isCancel(error)) {
          console.error(error);
          setComplianceText('Error loading compliance text');
        }
      } finally {
        setLoadingText(false);
      }
    }, 300);

    return () => {
      clearTimeout(timeout);
      source.cancel();
    };

  }, [selectedCarId, selectedTrimId, selectedPowertrainId, selectedTemplateId]);

  return (
    <AppLayout breadcrumbs={breadcrumbs}>
      <Head title="Create new compliance text template" />

      <div className="p-4">
        <div className="grid sm:grid-cols-4 gap-4">

          {/* TEMPLATE */}
          <div className="sm:col-span-2">
            <Select
              value={selectedTemplateId ? String(selectedTemplateId) : ""}
              onValueChange={(value) => setSelectedTemplateId(Number(value))}
            >
              <SelectTrigger className="w-full">
                <SelectValue placeholder="Select template" />
              </SelectTrigger>

              <SelectContent>
                {templates.map((template) => (
                  <SelectItem key={template.id} value={String(template.id)}>
                    {template.variant}
                  </SelectItem>
                ))}
              </SelectContent>
            </Select>
          </div>

          {/* CAR */}
          <div className="sm:col-span-2">
            <Select
              value={selectedCarId ? String(selectedCarId) : ""}
              onValueChange={handleCarChange}
              disabled={!selectedTemplateId}
            >
              <SelectTrigger className="w-full">
                <SelectValue placeholder="Select a car" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectLabel>Cars</SelectLabel>
                  {cars.map(car => (
                    <SelectItem key={car.id} value={String(car.id)}>
                      {car.name}
                    </SelectItem>
                  ))}
                </SelectGroup>
              </SelectContent>
            </Select>
          </div>

          {/* TRIM */}
          <div className="flex gap-2 sm:col-span-2">
            <Select
              value={selectedTrimId ? String(selectedTrimId) : ""}
              onValueChange={handleTrimChange}
              disabled={!selectedCar}
            >
              <SelectTrigger className="w-full">
                <SelectValue placeholder="Select a trim" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectLabel>Trims</SelectLabel>
                  {trims.map(trim => (
                    <SelectItem key={trim.id} value={String(trim.id)}>
                      {trim.name}
                    </SelectItem>
                  ))}
                </SelectGroup>
              </SelectContent>
            </Select>

            <Button
              variant="outline"
              size="icon"
              disabled={!selectedTrimId}
              onClick={() => {
                setSelectedTrimId(null);
                setSelectedPowertrainId(null);
              }}
            >
              <X />
            </Button>
          </div>

          {/* POWERTRAIN */}
          <div className="flex gap-2 sm:col-span-2">
            <Select
              value={selectedPowertrainId ? String(selectedPowertrainId) : ""}
              onValueChange={handlePowertrainChange}
              disabled={!selectedTrim}
            >
              <SelectTrigger className="w-full">
                <SelectValue placeholder="Select a powertrain" />
              </SelectTrigger>
              <SelectContent>
                <SelectGroup>
                  <SelectLabel>Powertrains</SelectLabel>
                  {powertrains.map(pt => (
                    <SelectItem key={pt.id} value={String(pt.id)}>
                      {pt.engine.name}
                    </SelectItem>
                  ))}
                </SelectGroup>
              </SelectContent>
            </Select>

            <Button
              variant="outline"
              size="icon"
              disabled={!selectedPowertrainId}
              onClick={() => {
                setSelectedPowertrainId(null);
              }}
            >
              <X />
            </Button>
          </div>

          {/* TEXT OUTPUT */}
          <div className="sm:col-span-4">
            <div className="relative">
              <Textarea
                value={complianceText ?? ""}
                readOnly
                className="min-h-[200px] bg-muted/30 text-sm font-mono pr-20"
              />

              <Button
                type="button"
                size="sm"
                variant="ghost"
                onClick={handleCopy}
                className="absolute top-2 right-2"
              >
                {copied ? (
                  <Check className="h-4 w-4 text-green-500" />
                ) : (
                  <Copy className="h-4 w-4" />
                )}
              </Button>
            </div>
          </div>

          {/* EMBED OUTPUT */}
          <div className="sm:col-span-4 space-y-2">
            <div className="relative">
              <Textarea
                value={embedCode}
                readOnly
                className="min-h-[120px] font-mono text-xs"
              />

              <Button
                size="sm"
                variant="ghost"
                onClick={handleEmbedCopy}
                className="absolute top-2 right-2"
              >
                {copiedEmbed? (
                  <Check className="h-4 w-4 text-green-500" />
                ) : (
                  <Copy className="h-4 w-4" />
                )}
              </Button>
            </div>
          </div>

        </div>
      </div>
    </AppLayout>
  );
}