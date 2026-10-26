<?php

namespace App\Services;

class SeederDiffService
{
    /**
     * Compute unified diff between two code strings.
     */
    public function computeDiff(?string $oldCode, ?string $newCode): array
    {
        $oldLines = $oldCode !== null ? preg_split("/\r\n|\n|\r/", $oldCode) : [];
        $newLines = $newCode !== null ? preg_split("/\r\n|\n|\r/", $newCode) : [];

        $diff = [];
        $oldIndex = 0;
        $newIndex = 0;
        $oldCount = count($oldLines);
        $newCount = count($newLines);

        $addedCount = 0;
        $removedCount = 0;

        // Simple longest-common-subsequence based diff
        $lcs = $this->computeLCS($oldLines, $newLines);

        $i = 0;
        $j = 0;
        $oldLineNum = 1;
        $newLineNum = 1;

        while ($i < $oldCount || $j < $newCount) {
            if ($i < $oldCount && $j < $newCount && $oldLines[$i] === $newLines[$j]) {
                $diff[] = [
                    'type' => 'unchanged',
                    'old_no' => $oldLineNum++,
                    'new_no' => $newLineNum++,
                    'line' => $oldLines[$i],
                ];
                $i++;
                $j++;
            } elseif ($j < $newCount && ($i >= $oldCount || !in_array($newLines[$j], array_slice($oldLines, $i)))) {
                $diff[] = [
                    'type' => 'added',
                    'old_no' => null,
                    'new_no' => $newLineNum++,
                    'line' => $newLines[$j],
                ];
                $addedCount++;
                $j++;
            } elseif ($i < $oldCount) {
                $diff[] = [
                    'type' => 'removed',
                    'old_no' => $oldLineNum++,
                    'new_no' => null,
                    'line' => $oldLines[$i],
                ];
                $removedCount++;
                $i++;
            }
        }

        return [
            'lines' => $diff,
            'added' => $addedCount,
            'removed' => $removedCount,
            'has_changes' => ($addedCount > 0 || $removedCount > 0),
        ];
    }

    protected function computeLCS(array $a, array $b): array
    {
        // Simple matrix computation for moderate file sizes
        $m = count($a);
        $n = count($b);
        if ($m > 1500 || $n > 1500) {
            return []; // Skip matrix on huge files to preserve memory
        }

        $c = array_fill(0, $m + 1, array_fill(0, $n + 1, 0));

        for ($i = 1; $i <= $m; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                if ($a[$i - 1] === $b[$j - 1]) {
                    $c[$i][$j] = $c[$i - 1][$j - 1] + 1;
                } else {
                    $c[$i][$j] = max($c[$i][$j - 1], $c[$i - 1][$j]);
                }
            }
        }

        return $c;
    }
}
