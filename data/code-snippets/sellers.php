<?php
$fol = nodeValue();
if (!disk_is_dir($base = SITEPATH . '/data/internal/' . $fol . '/'))
    return bootstrapAndUX::colouredDiv('Internal Folder Missing: ' . $fol, bootstrapAndUX::danger);
if (!disk_file_exists($base . '/sellers.tsv'))
    return bootstrapAndUX::colouredDiv('Sellers File Missing: ' . $fol . '/sellers.tsv', bootstrapAndUX::danger);

//sno	name	status	contact	email	phone
$sheet = getSheet($base . '/sellers.tsv', false);
$op = []; //['&num;' . implode('|', array_keys($sheet->columns)), ''];

foreach ($sheet->rows as $item) {
	$item = $sheet->asObject($item);
	$op[] = '### ' . $item['sno'] . '. ' . $item['name'] . NEWLINE
		. ' - ' . makeLink('WhatsApp', whatsapp_me($item['phone'], humanize(nodeValue()) . ' [' . site::$current->name . ']')) . ' ' . textBoxWithCopyOnClick(false, $item['phone']) . NEWLINE
		. ' - ' . makeLink('Search', replaceItems(variableOr('SearchMail', '#todo-search-email=%email%'), ['email' => $item['email']])) . ' ' . textBoxWithCopyOnClick(false, $item['email']) . NEWLINE
		. ' - Status: ' . $item['status'] . NEWLINE
		. ' - Contact: ' . $item['contact'] . NEWLINE;
}
    

variableAppend(VAREngageFields, [], 'sellers', returnLines(implode(NEWLINE, $op)));

return bootstrapAndUX::colouredDiv(count($sheet->rows) . ' Seller(s) Loaded', bootstrapAndUX::success);
