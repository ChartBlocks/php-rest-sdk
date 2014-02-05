<?php
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    include(__DIR__ . '/../vendor/autoload.php');
} elseif (file_exists(__DIR__ . '/../../autoload.php')) {
    include(__DIR__ . '/../../autoload.php');
} else {
    throw new \Exception('Could not find autoload.php');
}


$config = array(
    'secret' => '26dc572367b3fcebe3ef8607c63c01cb',
    'token' => '52de8d29054ff3600a000001'
);

$dataSetId = '52f139ea054ff30f1f000004';

$client = new \ChartBlocks\Client($config);

$dataSet = $client->getDataSet($dataSetId);




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

$row->save();
$offset = 0;
$limit = 0;

$rowSet = $dataSet->select(array(
    'offset' => $offset,
    'limit' => $limit
        )
);

$rowSet->getRow(24)->getCell(0)->setValue('putting something in row 25')->save();

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
