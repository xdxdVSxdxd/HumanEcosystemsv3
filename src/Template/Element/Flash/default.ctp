<?php
$class = 'message';
if (!empty($params['class'])) {
    $class .= ' ' . $params['class'];
}
?>
<div class="row">
	<div class="col-md-2"></div>
	<div class="col-md-8 <?= h($class) ?> alert alert-info" role="alert" onclick="this.classList.add('hidden')"><?= h($message) ?></div>
	<div class="col-md-2"></div>
</div>

