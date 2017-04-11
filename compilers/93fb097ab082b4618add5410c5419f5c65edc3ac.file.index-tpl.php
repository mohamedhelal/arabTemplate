<?php
/**
* Create By ArabTemplate Version : 10
* Created Date : 09/04/2017
* File Path :'templates/index.tpl'
*/
/**
* includes files
*/

$_arTpl->setMeta(array('parent' => 'layout3.tpl'));

$_arTpl->setMeta( array('thisBlocks' =>  array(
'template_block_02083f4579e08a612425c0c1a17ee47add783b94',
'template_block_732c4de9ef72a77f4d9ad135c5b29bc31f4bc546'))
);
if(!function_exists('_template_content_e43b807af9cc8df7d350c3baf9e47f167c9520a0')){
function _template_content_e43b807af9cc8df7d350c3baf9e47f167c9520a0(&$_arTpl){?>


<?php /* {block 'body'} templates/index.tpl*/

if(!function_exists('template_block_02083f4579e08a612425c0c1a17ee47add783b94')){
function template_block_02083f4579e08a612425c0c1a17ee47add783b94(&$_arTpl){?>

    <h1>Body My Index</h1>
<?php } }
/*{/block 'body'}*/
?>

<?php /* {block 'body2'} templates/index.tpl*/

if(!function_exists('template_block_732c4de9ef72a77f4d9ad135c5b29bc31f4bc546')){
function template_block_732c4de9ef72a77f4d9ad135c5b29bc31f4bc546(&$_arTpl){?>

    <h1>Body2 My Index</h1>
<?php } }
/*{/block 'body2'}*/
?>

<?php /* {block 'body3'} templates/index.tpl*/

if(!function_exists('template_block_ced15f63ace1de7af9e76cd7fcd2cee8d34214df')){
function template_block_ced15f63ace1de7af9e76cd7fcd2cee8d34214df(&$_arTpl){?>

    <h1>Body3 My Index</h1>
<?php } }
/*{/block 'body3'}*/
?><?php $_arTpl->display($_arTpl->varTpl['layout']->value);?>


<?php } } ?>