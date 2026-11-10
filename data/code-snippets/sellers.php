<?php
$fol = nodeValue();
if (!disk_is_dir($base = SITEPATH . '/data/internal/' . $fol . '/'))
    return bootstrapAndUX::colouredDiv('Internal Folder Missing: ' . $fol, bootstrapAndUX::danger);
if (!disk_file_exists($base . '/sellers.tsv'))
    return bootstrapAndUX::colouredDiv('Sellers File Missing: ' . $fol . '/sellers.tsv', bootstrapAndUX::danger);

$sheet = getSheet($base . '/sellers.tsv', false);
$op = ['&num;' . implode('|', array_keys($sheet->columns)), ''];

foreach ($sheet->rows as $item)
    $op[] = '* ' . implode('|', $item);

variableAppend(VAREngageFields, [], 'sellers', returnLines(implode(NEWLINE, $op)));

return bootstrapAndUX::colouredDiv(count($sheet->rows) . ' Seller(s) Loaded', bootstrapAndUX::success);
