<?php

namespace Modules\Gestel\Imports;

use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithBatchInserts;
use Maatwebsite\Excel\Concerns\WithChunkReading;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Modules\Gestel\Entities\TelAutoFactura;

/**
 * 
 */
class TelAutoFacturaImport implements ToModel, WithStartRow, WithCustomCsvSettings, WithChunkReading, WithBatchInserts, ShouldQueue
{

  private $year = null;
  private $month = null;

  public function __construct($month = null, $year = null)
  {
    $this->year = $year ? $year : null;
    $this->month = $month ? $month : null;
  }
  /**
   * startRow
   */
  public function startRow(): int
  {
    return 2;
  }

  public function getCsvSettings(): array
  {
    return [
      'delimiter' => ','
    ];
  }
  /**
   * @param array $row
   *
   * @return User|null
   */
  public function model(array $row)
  {
    return new TelAutoFactura([
      'telf' => $row[3],
      'importe' => $row[15],
      'noft' => $row[1],
      'dia' => $row[5],
      'mes' => $this->month ? $this->month : $row[6],
      'year' => $this->year ? $this->year : Carbon::now()->year,
      'dest' => $row[7],
      'slla' => $row[8],
    ]);
  }
  /**
   * 
   */
  public function chunkSize(): int
  {
    return 1000;
  }
  /**
   * 
   */
  public function batchSize(): int
  {
    return 1000;
  }
}
