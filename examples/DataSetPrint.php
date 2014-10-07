<?php
include('_boostrap.php');

$config = array(
    'token' => '54326600c9a61d1025d2d25a',
    'secret' => '7c7917c8d1ae3fe8c9aea94fb49221be',
);

$dataSetId = '542bd8ebc9a61d2d03d5c969';

$client = new \ChartBlocks\Client($config);

/* @var $dataSet \ChartBlocks\Entity\DataSet */
$dataSet = $client->dataSet->findById($dataSetId);

echo $dataSet->name . PHP_EOL;
echo '=======================' . PHP_EOL;

$rows = $dataSet->getData()->select();

foreach ($rows as $row) {
    echo 'Row ' . $row->getRowNumber() . PHP_EOL;
    echo '-----------------------' . PHP_EOL;

    $columns = array();
    foreach ($row as $cell) {
        $columns[] = $cell->getColumnNumber() . ': ' . $cell;
    }

    echo implode(' | ', $columns) . PHP_EOL;
    echo PHP_EOL;
}

exit;

$row = $dataSet->createRow();

$row->getCell(1)
        ->setValue('woot');
$row->getCell(3)
        ->setValue('hmm woot');
$row->getCell(5)
        ->setValue('woot hmm');
$row->getCell(7)
        ->setValue('yay woot');
$row->getCell(9)
        ->setValue('woot yay');

// $row->save();
$offset = 0;
$limit = 0;

$rowSet = $dataSet->select(array(
    'offset' => $offset,
    'limit' => $limit
        )
);

//$rowSet->getRow(24)->getCell(2)->setValue('putting something in row 25')->save();

$i = $offset + 1;
?>

<table  border="1">
    <tbody>
        <?php foreach ($rowSet as $row): ?>
            <tr>
                <td><?php echo $i++ ?></td>
                <?php foreach ($row->getCells() as $cell): ?>
                    <td><?php echo $cell->getValue(); ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
