<?php
// NEEDS DEFINED $association_object!

$associations = Association::getAssociationsForObject(get_class($association_object), $association_object->getId());
?>
<script>
	$(function() {
		$("a#association_hiddenclicker").fancybox({
			'type'    : 'iframe',
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
			'speedIn'		:	600, 
			'speedOut'		:	200, 
			'height'		:	350, 
			'overlayShow'	:	true,
			'helpers'		:   { overlay:null, closeClick:true }
		});
	});
	function callBoxFancy(my_href) {
		var j1 = document.getElementById("association_hiddenclicker");
		j1.href = my_href;
		$('#association_hiddenclicker').trigger('click');
	}
</script>

  <div id="association_hidden_clicker" style="display:none"><a id="association_hiddenclicker" href="http://www.google.com" >Hidden Clicker</a></div>
  <li class="dropdown">
    <a href="#" data-toggle="dropdown" class="dropdown-toggle">Verknüpfungen (<?php echo count($associations);?>)<b class="caret"></b></a>
    <ul class="dropdown-menu">
    <?php 
        if (count($associations)>0){
            foreach ($associations as $association){
                if ($association->getModule1() == get_class($association_object) && $association->getObjectid1() == $association_object->getId()){
                    $classname = $association->getModule2();
                    $object = new $classname($association->getObjectid2());
                    $link_href = Association::getPath($classname);
                    $object_name = Association::getName($object);
                } else {
                    $classname = $association->getModule1();
                    $object = new $classname($association->getObjectid1());
                    $link_href = Association::getPath($classname);
                    $object_name = Association::getName($object);
                }
                echo '<li><a href="index.php?page='.$link_href.$object->getId().'" target="_blank">';
                echo '> ' . $object_name;
                echo '</a></li>';
            }
        }
        echo '<li><a href="#" onclick="callBoxFancy(\'libs/modules/associations/association.frame.php?module='.get_class($association_object).'&objectid='.$association_object->getId().'\');">> NEU</a></li>';
    ?>
    </ul>
  </li>
  </br>