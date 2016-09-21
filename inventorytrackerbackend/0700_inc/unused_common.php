<?php
function cleanWord($word)
{
	if(strpos($word, ';') === false) return $word;
	$word = str_replace(';','',$word);
	return $word;
}

function dirToArray($dir) { 
   
   $result = array(); 

   $cdir = scandir($dir); 
   foreach ($cdir as $key => $value) 
   { 
      if (!in_array($value,array(".",".."))) 
      { 
         if (is_dir($dir . DIRECTORY_SEPARATOR . $value)) 
         { 
            $result[$value] = dirToArray($dir . DIRECTORY_SEPARATOR . $value); 
         } 
         else 
         { 
            $result[] = $value; 
         } 
      } 
   } 
   
   return $result; 
} 

function getDir($path)
{
	$results = scandir($path);
	$dir = array();
	foreach ($results as $result) {
	    if ($result === '.' or $result === '..' || $result == 'syllabus' || $result == 'resources') continue;
	
	    if (is_dir($path . $result)) { //originally -> is_dir($path . '/' . $result)
	        $dir [] = $result;
	    }
	}
	return $dir;
}
function replaceTitle($word,$count)
{
	$word = makeReadable($word);
	$count = $count + strpos($word,' ');
	$word = substr($word, $count, -4);
	$word = ucwords($word);
	return $word;
}

function makeReadable($word)
{
	$word = str_replace("_", " ", $word);
	$word = str_replace("-", " ", $word);
	return $word;
}

function stripPath($word)
{
	if(strpos($word, '/') === false) return null;
	$index = strpos($word, '/');
	return substr($word, 0, $index);
}

function makePageLinks($location, $directoryPath, $path)
{
	$directories = getDir($location . $directoryPath);
	$links = '<div id="loader"><img src="../images/ajax-loader.gif" alt="loader" /></div>';
	$links .= '<div id="content-menu">
		<ul>';

	for ($i=0; $i < count($directories) ; $i++) {
		$links .= '<li><a class="ajaxLink" href="' . $directoryPath . '">'.$directories[$i]. '</a></li>';
	}
	$links .= getCommonNav($directoryPath) .'</ul>
		</div>
		<div class="clear"></div>';
		
		$links .= '<div id="ninja-menu"><p id="menu"></p></div><div id="ninja">
		<ul>';

	for ($i=0; $i < count($directories) ; $i++) {
		$links .= '<li><a class="ajaxLink" href="' . $directoryPath . '">'.$directories[$i]. '</a></li>';
	}
	$links .= getCommonNav($directoryPath) .'</ul>
		</div>
		<div class="clear"></div>';
	
	return $links;
}

function getCommonNav($directoryPath)
{
	$nav = '';
	$nav .= '<li><a class="ajaxLink" href="' . $directoryPath . '">resources</a></li>';
	$nav .= '<li><a class="ajaxLink" href="' . $directoryPath . '">tutoring</a></li>';
	$nav .= '<li><a class="ajaxLink" href="' . $directoryPath . '">contact</a></li>';
	return $nav;
}

function makeContentLinks($rootPath, $subPath)
{
	$path = $rootPath . $subPath;
	$files = dirToArray($path);
	$fileCount = count($files);
	$links = '<div class="column half" name="' . stripPath($subPath) . '"><ul class="content-list">';
	$secondCol = "";
	if($fileCount == 0 && $path != 'content-title') return $links . '<li>No Items Found</li></ul></div>';
	if(strpos($subPath,'assignment') !== false)
	{
		$fileCount = getNumAssignments(substr($rootPath,0,-1),$fileCount);
	}
	if($fileCount > 10) $secondCol = '</ul></div><div class="column half"><ul class="content-list content-col2">';
	
	
	for ($i=0; $i < $fileCount ; $i++) 
	{
		if((int)($fileCount/2)+1 == $i) $links .= $secondCol; 
		$count = 0;
		if(strpos($path,'lectures')) $count = 12;
		if(strpos($files[$i],'.docx') || strpos($files[$i],'_log') ||strpos($files[$i],'.php')) continue;
		if(strpos($files[$i], 'wav'))$links .= '<li><a class="ajaxLink" href="../'.$path . $files[$i].'" target="blank">' . makeReadable($files[$i]).'</a></li>';
		else if($subPath == 'demos/') $links .= '<li><a class="ajaxLink" href="../demos.php?path='.$rootPath.'&file=' . $files[$i] .'" target="blank">' . makeReadable($files[$i]).'</a></li>';
		else $links .= '<li><a class="actualLink" href="../'.$path . $files[$i].'" target="blank">' .replaceTitle($files[$i], $count).'</a></li>';
	}
	$links .= '</ul></div';
	return $links;
}

function makeAdminSection($rootPath, $subPath)
{
	$section = '<h1>' . $rootPath . '</h1>';
	$section .= getDropDown($rootPath);
	$section .= getUploadButton($rootPath);
	return $section;
}

function getUploadButton($course)
{
	$section = '<form action="upload_file.php?course='.$course.'" method="post"
	enctype="multipart/form-data">
	<label for="file">Filename:</label>
	<input type="file" name="file" id="file"><br>
	<input type="submit" name="submit" value="Submit">
	</form>';
	return $section;
}
function getDropDown($course)
{
	$num = count(dirToArray('../'.$course.'/assignments/pdf/'));
	$current = getNumAssignments($course,$fileCount);
	$content = '<h2>Assignments Shown</h2>
		<form action="" method="POST">
<select name="section">';
	for ($i=1; $i <= $num; $i++) {
		if(($i) == $current) $content .= '<option value="'.$i.'" selected>'.$i.'</option>';
		else $content .= '<option value="'.$i.'">'.$i.'</option>';
	}
	$content .= '</select>
	<input type="hidden" name="course" value="'.$course.'">
	<input type="submit" name="submit" value="Update">
</form>
	';
	return $content;
}

function getNumAssignments($course,$fileCount)
{
	$num = $fileCount;
	$sql = 'SELECT NumAssignments from Course where CourseName = "'. $course .'"';
	$dbo = getSqlData($sql,new container());
	if($dbo->getItems() != NULL)
	{
		$row = $dbo->getItems();
		for($i = 0; $i < count($row);$i++)
		{
			$num = $row[$i][0];
		}
	}
	return $num;
}

function getResources($rootPath)
{
	$content = '';
	$subPath = 'syllabus';
	$files = dirToArray($rootPath . $subPath);
	$content = '<div class="column half" name="resources' . ''/*stripPath($subPath)*/ . '"><ul class="content-list">';
	if(count($files) == 0 && $path != 'content-title') return $links . '<li>No Items Found</li></ul></div>';
	for ($i=0; $i < count($files) ; $i++) 
	{
		if(strpos($files[$i],'.docx')) continue;
		
		$content .= '<li><a class="actualLink" href="../'.$rootPath .$subPath. $files[$i].'" target="blank">' . substr($files[$i],0,-4) .'</a></li>';
	}
	$sql = 'SELECT Resource.ResourceName, Resource.ResourceSrc FROM Course JOIN Resource on Course.CourseKey = Resource.CourseKey WHERE Course.CourseName = "'. substr($rootPath, 0,-1) .'"';
	
	$dbo = getSqlData($sql,new container());
	if($dbo->getItems() != NULL)
	{
		$row = $dbo->getItems();
		for($i = 0; $i < count($row);$i++)
		{
			$content .= '<li><a href="' . $row[$i][1] . '" target="_blank">'. $row[$i][0] . '</a></li>';
		}
	}
	
	$content .= '</ul></div';
	return $content;
}

function getContact($office = true,$subPath)
{
	$location = 'Office';
	$sql = 'SELECT FirstName, UserEmail, RoomNum, UserHours,ImageSrc FROM `Person` WHERE PersonKey';
	if(!$office) 
	{
		$sql .= ' != 1';
		$location = 'Room';
	}
	else $sql .= ' = 1';
	
	$content = '';
	$dbo = getSqlData($sql,new container());
	if($dbo->getItems() == NULL) return '<div align="center">Sorry, no Tutors for ' . $subPath .'</div>';
	$row = $dbo->getItems();
	for($i = 0; $i < count($row);$i++)
	{
		$content .= '<div class="column third info-image" name="' . stripPath($subPath) . 
		'"><img src="../images/'.$row[$i][4].'"></div>';
		$content .= '<div class="column two-thirds info">
			<ul>	
				<li><span>Name:</span> '.ucfirst($row[$i][0]).'</li>
				<li><span>Email:</span> <a href="mailto:'.$row[$i][1].'">'.$row[$i][1].'</a></li>
				<li><span>'.$location.':</span> '.$row[$i][2].'</li>
				<li><span>'.$location.' hours:</span> '.$row[$i][3].'</li>
			</ul></div><div class="clear"></div>';
	}
			
	return $content;	
}

function getSqlData2($sql,$model)
{
	$iConn = conn();  

	$result = mysqli_query($iConn,$sql) or die(trigger_error(mysqli_error($iConn), E_USER_ERROR));

	if(mysqli_num_rows($result) > 0)
	{
		while($row = mysqli_fetch_assoc($result))
		{
			$single = array();
			foreach ($row as $key => $value) 
			{
				$single[] = $row[$key];
			}
			$model->addItem($single);		
		}
	}	
	@mysqli_free_result($result);
	return $model;
}
function createUL($node)
{
	echo '<li class="todo-navigation" href="'.$node->getId().'">'. $node->getTitle() .'';
	$children = $node->getChildren();
	if(count($children) > 0)
	{
		echo '<ul id="list-'.$node->getId().'"><h4 class="back-navigation">^ '.$node->getTitle().'</h4>';
		for ($i=0; $i < count($children) ; $i++) { 
			createUL($children[$i]);
		}
		echo '</li></ul>';
	}
	//echo '</li>';
}

function testTree($node,$tab)
{
	echo $tab . $node->getTitle() . '<br>';
	$child = $node->getChildren();
	for ($i=0; $i < count($child) ; $i++) { 
		testTree($child[$i],$tab .'*');
	}
}
function getMCourseNav()
{
	$navigation = '';
	$navigation .= '<!-- super secret ultimate ninja nav -->
<section>

    <div id="ninja-tn">
        <h2 class="trigger">Learning++</h2>
    
        <ul id="ninja-tn-menu">';
		for ($i=0; $i < count($navItems); $i++) { 
			$navigation .= '<li class="ninja-tn-item"><a href="#">'.$navItems[$i]->getTitle().'</a>
	                <ul class="sub-menu"><!--add "hidden" back into class-->';
			for ($j=0; $j < $navItems[$i]->getNumSubs(); $j++) 
			{ 
				$navigation .= '<li><a href="'.$location.$navItems[$i]->getFolder($j).'">'.$navItems[$i]->getSubTitle($j).'</a></li>';
			}
			$navigation .= '</ul></li>';
		}
		$navigation .='            
        </ul>
    </div>	
	
</section>';
	echo $navigation;
}
?>