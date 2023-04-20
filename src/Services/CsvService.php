<?php

namespace Aparlay\Core\Services;

final class CsvService
{
    private array $headers = [];
    private array $data    = [];

    public function __construct(
        private string $separator = ',',
        private string $eol = "\n"
    ) {
    }

    public function setHeaders(array $headers): self
    {
        $this->headers = $headers;

        return $this;
    }

    public function setHeadersFromData()
    {
        $this->headers = array_keys($this->data);

        return $this;
    }

    public function addRow(array $row)
    {
        $this->data[] = $row;

        return $this;
    }

    public function setData(array $data)
    {
        $this->data = $data;
    }

    public function generate(): string
    {
        $data = array_merge([$this->headers], $this->data);

        $rows = array_map(function ($row) {
            return implode($this->separator, $row);
        }, $data);

        return implode($this->eol, $rows);
    }

    public function downloadCsv(string $name)
    {
        return response()->streamDownload(function () {
            echo $this->generate();
        }, $name);
    }
}
