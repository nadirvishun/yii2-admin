<?php
/**
 * 自动生成语言文件的模板
 */

/* @var $labels string[] list of attribute labels (name => label) */

echo "<?php\n";
?>
/**
* 语言文件
*/
return [
<?php foreach ($labels as $name => $label): ?>
    <?= "'".$label."' => '请输入中文',\n" ?>
<?php endforeach; ?>
];
