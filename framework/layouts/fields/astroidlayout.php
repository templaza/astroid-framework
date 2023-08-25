<?php extract($displayData);
$form_template = array();
$astroidElements = Astroid\Helper::getAllAstroidElements();
foreach ($astroidElements as $astroidElement) {
    $form_template[$astroidElement->type] = $astroidElement->renderJson();
}
$json =   [
    'name'    =>  $name,
    'value'   =>  $options,
    'type'    =>  strtolower($type),
    'form'    =>  $form_template
];
var_dump($json); die();
?>
<script>
   var _layout = <?php echo json_encode($options); ?>;
   var AstroidLayoutBuilderElements = [];
<?php
$astroidElements = Astroid\Helper::getAllAstroidElements();

foreach ($astroidElements as $astroidElement) {
   echo 'AstroidLayoutBuilderElements.push(' . json_encode($astroidElement->getInfo()) . ');';
}
?>
</script>
<?php foreach ($astroidElements as $astroidElement) { ?>
   <script type="text/ng-template" id="element-form-template-<?php echo $astroidElement->type; ?>">
   <?php echo $astroidElement->renderForm(); ?>
   </script>
<?php } ?>

<?php $sectionElement = new AstroidElement('section'); ?>
<script type="text/ng-template" id="element-form-template-section">
   <?php echo $sectionElement->renderForm(); ?>
</script>

<?php $rowElement = new AstroidElement('row'); ?>
<script type="text/ng-template" id="element-form-template-row">
   <?php echo $rowElement->renderForm(); ?>
</script>

<?php $columnElement = new AstroidElement('column'); ?>
<script type="text/ng-template" id="element-form-template-column">
   <?php echo $columnElement->renderForm(); ?>
</script>