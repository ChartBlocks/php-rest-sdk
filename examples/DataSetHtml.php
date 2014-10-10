<?php

use ChartBlocks\Client;
use ChartBlocks\DataSet\Cell;

include('_boostrap.php');

$config = array(
    'token' => 'YOUR_TOKEN',
    'secret' => 'YOUR_SECRET',
);

$dataSetId = '542bd8ebc9a61d2d03d5c969';

$client = new \ChartBlocks\Client($config);

/* @var $dataSet \ChartBlocks\Entity\DataSet */
$dataSet = $client->dataSet->findById($dataSetId);
$rows = $dataSet->getData()->select();
?>

<h1><?php echo $dataSet->name; ?></h1>

<table>
    <thead>
        <tr>
            <th>#</th>
            <?php for ($i = 1; $i < $rows->getMaxColumns(); $i++): ?>
                <th><?php echo Cell::columnNumberToLetter($i); ?></th>
            <?php endfor; ?>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($rows as $row): ?>
            <tr>
                <td><?php echo $row->getRowNumber(); ?></td>
                <?php foreach ($row as $cell): ?>
                    <td><?php echo $cell; ?></td>
                <?php endforeach; ?>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<style>
    body{
        font-family: Arial, sans-serif;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 20px;
        font-size: 14px;
    }

    table tr:nth-child(odd) {
        background: rgba(0, 0, 0, 0.1);
    }

    table th {
        font-size: 1.2em;
        font-weight: 600;
        background: #25aae1;
        color: #fff;
        padding: 10px;
        vertical-align: top;
        text-align: left;
    }

    table td {
        border-bottom: 1px solid #dddddd;
        font-size: 1.2em;
        color: #a1a9ad;
        vertical-align: top;
        text-align: left;
        padding: 10px;
    }
</style>