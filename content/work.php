<?php
if (!is_page_secure()) {
	echo bootstrapAndUX::colouredDiv('Need to be logged in - contact admin', bootstrapAndUX::danger);
	return;
}

h2(humanize(nodeValue()), cssUX::CenterContainer);
contentBox(nodeValue(), cssUX::container);

DEFINE('STATUSCOLORS', [
	'Complete' => bootstrapAndUX::success,
	'Verifying Documents' => bootstrapAndUX::warning,
	'Working' => bootstrapAndUX::primary,
	'Not Started' => bootstrapAndUX::danger,
]);

function colorOf($item) { return 'm-1 btn btn-' . ($item == getPageParameterAt() ? 'success' : 'primary'); }
function statusColor($item) { return valueIfSet(STATUSCOLORS, $item['Status'], bootstrapAndUX::info); }

$items = _skipExcludedFiles(scandir(SITEPATH . '/data/internal/'), 'home', ONLYFOLDERS);

echo getLink('All Projects', pageUrl(nodeValue()), colorOf(false));
foreach ($items as $item)
	echo getLink(humanize($item), pageUrl(nodeValue() . '/' . $item), colorOf($item));


if ($fol = getPageParameterAt()) {
	$task = getPageParameterAt(2);

	$itemFormat = '<a href="' . pageUrl(nodeValue() . '/' . $fol) . '%Task%/">%Name% ~ #%Task%</a><br />by %Owner% on %Created%<br />%Status% at	%Updated%<br />Planned for: %Completion%';
	$sheet = getSheet("internal/$fol/work", 'Status');

	if (!$task) {
		echo cbCloseAndOpen(cssUX::container);
		h2('Details');
		renderFileSet(concatStrings('/', SITEPATH, 'data/internal', $fol, 'details') . '.');
	}

	foreach ($sheet->group as $grp => $items) {
		$groupStart = false;
		foreach ($items as $item) {
			//#Name	Task	Status	Created	Updated	Completion	Owner
			$item = $sheet->asObject($item);
			if ($task && $task != $item['Task']) continue;
			if (!$groupStart) {
				echo cbCloseAndOpen(cssUX::container);
				echo '<h3>' . $grp . ' (' . count($items) . ')</h3>' . NEWLINE;
				$groupStart = true;
			}

			echo bootstrapAndUX::colouredDiv(replaceItems($itemFormat, $item, WRAPREPLACE), statusColor($item));

			if ($task) {
				echo cbCloseAndOpen(cssUX::container);
				echo '<h3>' . $item['Name'] . '</h3>' . NEWLINE;
				renderFileSet(concatStrings('/', SITEPATH, 'data/internal', $fol, 'tasks', $item['Task']) . '.');
			}
		}
	}
}

function renderFileSet($fwe) {
	if (!disk_file_exists($fwe . 'md')) {
		echo bootstrapAndUX::colouredDiv('File Not Found: ' . $fwe . 'md', bootstrapAndUX::danger);
	} else {
		renderAny($fwe . 'md');
		if (!disk_file_exists($fwe . 'tsv'))
			echo bootstrapAndUX::colouredDiv('File Not Found: ' . $fwe . 'tsv', bootstrapAndUX::warning);
		else
			builtinOrRender($fwe . 'tsv', false, false);
	}
}

contentBox('end');
