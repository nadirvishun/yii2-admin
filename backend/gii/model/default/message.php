<?php
/**
 * 自动生成语言文件的模板
 */

/* @var $messages string[] list of message (key => message) */

echo "<?php\n";
?>
/**
* 语言文件
*/
return [
<?php foreach ($messages as $key => $value): ?>
    <?= "'" . $key . "' => '" . $value . "',\n" ?>
<?php endforeach; ?>
];