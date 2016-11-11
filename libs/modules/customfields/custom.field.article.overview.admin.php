<?php
/**
 *  Copyright (c) 2016 Klein Druck + Medien GmbH - All Rights Reserved
 *  * Unauthorized modification or copying of this file, via any medium is strictly prohibited
 *  * Proprietary and confidential
 *  * Written by Alexander Scherer <ascherer@ipactor.de>, 2016
 *
 */
require_once 'libs/modules/customfields/custom.field.class.php';

$article_fields = CustomField::fetch([
	[
		'column'=>'class',
		'value'=>'Article'
	]
]);

?>

<div class="panel panel-default">
	<div class="panel-heading">
		<h3 class="panel-title">
			Artikel Zusatzinformationen
			 <span class="pull-right">
				<button class="btn btn-xs btn-success"
						onclick="document.location.href='index.php?page=libs/modules/customfields/custom.field.article.edit.admin.php';">
					<span class="glyphicons glyphicons-plus"></span>
					Neues Feld
				</button>
			</span>
		</h3>
	</div>
	<div class="table-responsive">
		<table class="table table-hover">
			<thead>
				<tr>
					<th>Feldname</th>
					<th>Typ</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($article_fields as $article_field) {?>
				<tr class="pointer" onclick="window.location.href='index.php?page=libs/modules/customfields/custom.field.article.edit.admin.php&id=<?php echo $article_field->getId();?>';">
					<td><?php echo $article_field->getName();?></td>
					<td>
						<?php
						switch ($article_field->getType()){
							case CustomField::TYPE_INPUT:
								echo 'Input';
								break;
							case CustomField::TYPE_SELECT:
								echo 'Dropdown';
								break;
							case CustomField::TYPE_CHECKBOX:
								echo 'Checkbox';
								break;
						}
						?>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</div>
</div>
