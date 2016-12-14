<?php
/*Settings*/
$group_id = GROUP_ID;//id группы
$redirect_to = 'gallerybs.php';
$api_v = "5.60";

if ($group_id > 0)
{
?>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html;charset=utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" type="text/css" href="gallerybs.css">
    <title>Api Gallery v0.5.3</title>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>

    <!-- Latest compiled and minified JavaScript -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>

    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css">

    <!-- Optional theme -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap-theme.min.css">

</head>
<body>
<div class="container">
    <div class="row col-md-12 col-sm-12"><br></div>
    <div class="row col-md-12 col-sm-12">
        <?php
        /*Функции*/
        function group()
        {
            global $mytoken;
            global $group_id;
            $fields = "description,members_count,site,links,counters";
            $group_get = "https://api.vk.com/method/groups.getById?group_id={$group_id}&fields={$fields}&v=$api_v";
            $group_info = json_decode(file_get_contents($group_get))->response;
            $gphoto = $group_info[0]->photo_medium;
            $gname = $group_info[0]->name;
            $gsname = $group_info[0]->screen_name;
            $ginfo = $group_info[0]->description;
            $gmembers = $group_info[0]->members_count;
            $gcounts = $group_info[0]->counters;
            $gsite = $group_info[0]->site;
            $glinks = $group_info[0]->links;

            echo('<div class="alert alert-info text-center">' . $gname . ' <abbr title="Информация о сообществе" data-toggle="modal" data-target="#myModal">(подробнее)</abbr></div>');


            echo('
			<!-- Modal -->
			<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
				<div class="modal-dialog" role="document">
			    	<div class="modal-content">
			    		<div class="modal-header">
			        		<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			        		<h4 class="modal-title" id="myModalLabel"><a href="https://vk.com/club' . $group_id . '">' . $gname . '</a></h4>
			      		</div>
			    		<div class="modal-body">
			    			<div class="col-md-3">
								<a href="https://vk.com/club' . $group_id . '"><img class="img-responsive img-thumbnail" src="' . $gphoto . '" style="float:left"></a>
							</div>
							<div class="col-md-9">
				    			<!-- Nav tabs -->
								<ul class="nav nav-tabs" role="tablist">
									<li role="presentation" class="active"><a href="#about" aria-controls="about" role="tab" data-toggle="tab">Описание</a></li>
									<li role="presentation"><a href="#links" aria-controls="links" role="tab" data-toggle="tab">Ссылки</a></li>
									<li role="presentation"><a href="#more" aria-controls="more" role="tab" data-toggle="tab">Дополнительно</a></li>
								</ul>

								<!-- Tab panes -->
								<div class="tab-content">
									<div role="tabpanel" class="tab-pane active" id="about">
				      					<p class="text-center lead">
				      						<i>' . $ginfo . '</i>
										</p>
									</div>
									<div role="tabpanel" class="tab-pane" id="links">
									<p class="text-center lead">');
            foreach ($glinks as $links) {
                echo '<a href="' . $links->url . '">' . $links->name . '</a>. ' . $links->desc . '<br>';
            }
            echo('
										</p>
									</div>
									<div role="tabpanel" class="tab-pane active" id="more">
				      					<p class="text-center lead">
				      						id группы: ' . $group_id . '<br>
				      						url: https://vk.com/' . $gsname . '<br>
				      						Всего участников: ' . $gmembers . '<br>
				      						');

            foreach ($gcounts as $counts => $key) {
                echo $counts . ': ' . $key . '<br>';
            }
            echo('
										</p>
									</div>
								</div>
							</div>
						</div>
			    		<div class="modal-footer">
			        		<button type="button" class="btn btn-default" data-dismiss="modal">Понятно</button>
				    	</div>
				    </div>
				</div>
			</div>
			');
        }

        function albums() //Грузим список альбомов  превьюшками, названием и количемством фоток
        {
            global $mytoken;
            global $group_id;
            $albums_get = "https://api.vk.com/method/photos.getAlbums?gid={$group_id}&need_covers=1&v=$api_v";//поучаем список альбомов и обложки к ним. 
            $albums_list = json_decode(file_get_contents($albums_get))->response;
            $total = count($albums_list); //подсчет количества альбомов
            $var = $total;
            $var--;
            echo('<div class="col-md-3 col-sm-3"><div class="panel panel-primary">
				<div class="panel-heading"><b>Альбомы</b> <span class="badge" style="float:right">' . $total . '</span></div><div class="panel-body albums_list">');
            for ($i = 0; $i <= $var; $i++) {
                $alb_id = $albums_list[$i]->aid;
                $alb_title = $albums_list[$i]->title;
                $alb_thumb = $albums_list[$i]->thumb_src;
                $alb_size = $albums_list[$i]->size;
                $poz = $i + 1;
                echo('<a href="' . $redirect_to . '?album=' . $alb_id . '" >' . $poz . '. ' . $alb_title . '</a><br>
					<a href="' . $redirect_to . '?album=' . $alb_id . '" ><img class="img-responsive img-thumbnail" src="' . $alb_thumb . '"></a><br>
					<span class="badge">' . $alb_size . ' фото</span><hr>');
            }
            echo('</div></div></div>');
        }

        function photos() //Загружаем превьюшки фоток из выбранного альбома
        {
            global $mytoken;
            global $group_id;
            $alb_id = $_GET['album'];//хаваем ID альбома из url
            if (isset($alb_id)) {
                $photos_get = "https://api.vk.com/method/photos.get?gid={$group_id}&aid={$alb_id}&v=$api_v";//получаем информацию по каждой фотке
                $photos_list = json_decode(file_get_contents($photos_get))->response;
                $total = count($photos_list);
                $var = $total;
                $var--;
                echo('<div class="col-md-9 col-sm-9"><div class="panel panel-primary">
					<div class="panel-heading"><b>Фотографии альбома</b> <span class="badge" style="float:right">' . $total . '</span></div><div class="panel-body photos_list">');
                for ($i = 0; $i <= $var; $i++) {
                    $pic_id = $photos_list[$i]->pid;
                    $pic_thmb = $photos_list[$i]->src_small;
                    $pic_src = $photos_list[$i]->src;
                    echo('<a href="' . $redirect_to . '?album=' . $alb_id . '&photo=' . $pic_id . '&order=' . $i . '"><img class="img-responsive img-thumbnail" src="' . $pic_src . '"></a>');
                }
                echo('</div></div></div>');
            }
        }

        function show()
        {
            global $mytoken;
            global $group_id;
            $alb_id = $_GET['album'];
            $photo_id = $_GET['photo'];
            $order = $_GET['order'];
            if (isset($photo_id)) {
                echo('<div class="col-md-12 col-sm-12"><div class="panel panel-primary">
					<div class="panel-heading"><b>Просмотр фотографии</b></div><div class="panel-body show_photo">');
                $photo_load = "https://api.vk.com/method/photos.get?gid={$group_id}&aid={$alb_id}&pid={$photo_id}&limit=1&offset={$order}&v=$api_v";
                $photo_loaded = json_decode(file_get_contents($photo_load))->response;
                $total = count($photo_loaded);
                $var = $total;
                $var--;
                for ($i = 0; $i <= $var; $i++) {
                    $pic_id = $photo_loaded[$i]->pid; //id фотки
                    $pic_own = $photo_loaded[$i]->owner_id; // id Автора
                    $pic_thmb = $photo_loaded[$i]->src_small;//мелкая превьюшка
                    $pic_src = $photo_loaded[$i]->src;//большая превьюшка
                    $pic_big = $photo_loaded[$i]->src_big;//стандарнвй размер
                    $pic_txt = $photo_loaded[$i]->text; //Название
                    $pic_date = $photo_loaded[$i]->created;//Дата добавления
                    //не у всех есть. нужна проверка на наличие
                    $pic_xbig = $photo_loaded[$i]->src_xbig;//HD1
                    $pic_xxbig = $photo_loaded[$i]->src_xxbig;//HD2
                    echo('<img class="img-responsive img-thumbnail" src="' . $pic_big . '">');
                    break;
                }
                echo('</div></div></div>');
            }
        }

        /*страница*/
        group();
        albums();
        photos();
        echo '</div><div class="row col-md-12 col-sm-12">';
        show();
        
        }
        else echo "Не указан ID группы";
        ?>
    </div>
</div>
</body>
</html>