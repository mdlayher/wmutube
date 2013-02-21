<?
/**
 * scaffolder view page
 * @ignore
 * @package kata_scaffold
 * @author mnt
 */
?>
<table>
<?php

//TODO far from begin complete, and not very clean

echo '<tr>';
foreach ($schema['cols'] as $name=>$col) {
	echo '<td>'.$name.'</td>';
}
echo '<td></td><td></td></tr>';

foreach ($data as $line) {
    echo '<tr>';

    foreach ($schema['cols'] as $name=>$col) {
    	echo '<td>';
        switch ($col['type']) {
        	case 'int':
        	case 'string':
        	case 'text':
        	case 'date':
        	case 'enum':
                echo $line[$name];
                break;

        	case 'bool':
        		echo $line[$name]==0?'No':'Yes';
        		break;

        	case 'unixdate':
        		echo date('d.m.Y H:i',$line[$name]);
        		break;
        }
    	echo '</td>';
    }

    if ($schema['primary'] !== false) {
		$id = '';
		foreach ($schema['primary'] as $p) {
			$id = urlencode($line[$p]).'/';
		}

        echo '<td>'.$html->link('Edit',$this->params['controller'].'/update/'.$id).'</td>';
        echo '<td>'.$html->link('Delete',$this->params['controller'].'/delete/'.$id.'?page='.$page,array(),'Are you sure?').'</td>';

    }

    echo '</tr>';
}

echo '<tr><td colspan="'.(count($schema['cols'])+2).'" align="right">'.
    $html->link('Add Entry',$this->params['controller'].'/insert/').
    '</td></tr>';

?>
</table>
<br />
Page: <?php
for ($i=0;$i<$pages;$i++) {
    echo ' '.$html->link($i+1,$this->params['controller'].'/index/?page='.$i).' ';
}
?>
