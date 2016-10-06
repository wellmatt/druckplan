<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/tasks/task.class.php';

$todos = Task::getAllTasks(10,'due_date');

?>

<div class="panel panel-default">
	  <div class="panel-heading">
			<h3 class="panel-title">ToDo's</h3>
	  </div>
	  <div class="panel-body">
          <ul class="todo-list ui-sortable">
              <?php foreach($todos as $todo){
                  switch($todo->getPrio()){
                      case 1:
                          $class = 'label-primary';
                          break;
                      case 2:
                          $class = 'label-primary';
                          break;
                      case 3:
                          $class = 'label-success';
                          break;
                      case 4:
                          $class = 'label-success';
                          break;
                      case 5:
                          $class = 'label-info';
                          break;
                      case 6:
                          $class = 'label-info';
                          break;
                      case 7:
                          $class = 'label-warning';
                          break;
                      case 8:
                          $class = 'label-warning';
                          break;
                      case 9:
                          $class = 'label-danger';
                          break;
                      case 10:
                          $class = 'label-danger';
                          break;
                  }

                  ?>
                  <li>
                      <!-- todos text -->
                      <span class="text"><?php echo $todo->getTitle();?></span>
                      <!-- Emphasis label -->
                      <small class="label <?php echo $class;?>"><i class="fa fa-clock-o"></i>&nbsp;<?php echo date('d.m.y',$todo->getDue_date());?></small>
                      <!-- General tools such as edit or delete-->
                      <div class="tools">
                          <i class="fa fa-edit" onclick="document.location.href='index.php?page=libs/modules/tasks/task.overview.php&exec=edit&bid=<?php echo $todo->getId();?>';"></i>
                          <i class="fa fa-trash-o" onclick="document.location.href='index.php?page=libs/modules/tasks/task.overview.php&exec=delete&delid=<?php echo $todo->getId();?>&returnhome=true';"></i>
                      </div>
                  </li>
              <?php } ?>
          </ul>
	  </div>
</div>
