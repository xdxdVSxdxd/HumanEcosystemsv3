<?php
/**
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @since         0.10.0
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

/*
use Cake\Core\Configure;

echo Configure::read('App.imageBaseUrl');
$cakeDescription = 'Human Ecosystems:';


Configure::write('App.imageBaseUrl', 'webroot/img/');
*/

?>
<!DOCTYPE html>
<html>
<head>
    <?= $this->Html->charset() ?>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        <?= $this->fetch('title') ?>
    </title>
    <?= $this->Html->meta('icon') ?>

    <?= $this->Html->css('bootstrap.min.css') ?>
    <?= $this->Html->css('bootstrap-theme.min.css') ?>
    <?= $this->Html->css('bootstrap-toggle.min.css') ?>
    <?= $this->Html->css('custom.css') ?>
    <?= $this->Html->script('jquery-3.1.0.min.js') ?>
    <?= $this->Html->script('bootstrap.min.js') ?>
    <?= $this->Html->script('bootstrap-toggle.min.js') ?>
    <?= $this->Html->script('d3.min.js') ?>
    <?= $this->Html->script('app.js') ?>
    

    <?= $this->fetch('meta') ?>
    <?= $this->fetch('css') ?>
    <?= $this->fetch('script') ?>
</head>
<body>

    <nav class="navbar navbar-inverse">
      <div class="container">
        <div class="navbar-header">
          <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar" aria-expanded="false" aria-controls="navbar">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <div class="nav-brand">
          <?php  echo $this->Html->image('he_small.png', ['alt' => 'Human Ecosystems']);  ?>Human Ecosystems: <?= $this->fetch('title') ?></div>
        </div>
        <div id="navbar" class="navbar-collapse collapse">
        </div><!--/.navbar-collapse -->
      </div>
    </nav>
    <?= $this->Flash->render() ?>
    <div class="container first-container">
        <?= $this->fetch('content') ?>
    </div>
    <footer>
        <div class="row well">
            <div class="col-md-12">
                Human Ecosystems v3.0 – 2016
            </div>
        </div>
    </footer>
</body>
</html>
