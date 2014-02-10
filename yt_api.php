<?php
header("Content-Type: application/json");

function yt_search_api($txt, $start, $max){
	$res = array();
	$html = file_get_contents("http://gdata.youtube.com/feeds/api/videos?q=" . $txt . "&start-index=" . $start . "&max-results=" . $max . "&v=2&alt=json");
	$html = str_replace("$", "_", $html);
	$data = json_decode($html);
    for($i = 0; $i < count($data->feed->entry); $i++){
        $v = new stdClass();
        $v->url = $data->feed->entry[$i]->media_group->media_content[0]->url;
        $v->likes = $data->feed->entry[$i]->yt_rating->numLikes;
        $v->dislikes = $data->feed->entry[$i]->yt_rating->numDislikes;
        $v->views = $data->feed->entry[$i]->yt_statistics->viewCount;
        $v->favorites = $data->feed->entry[$i]->yt_statistics->favoriteCount;
        $v->rating = $data->feed->entry[$i]->gd_rating->average;
        $v->raters = $data->feed->entry[$i]->gd_rating->numRaters;
        $v->thumbnails = array();
        for($j = 0; $j < count($data->feed->entry[$i]->media_group->media_thumbnail); $j++)
            $v->thumbnails[] = $data->feed->entry[$i]->media_group->media_thumbnail[$j]->url;
        $v->title = $data->feed->entry[$i]->title->_t;
        $v->published = $data->feed->entry[$i]->published->_t;
        $v->updated = $data->feed->entry[$i]->updated->_t;
        $v->user_id = $data->feed->entry[$i]->author[0]->yt_userId->_t;
        $v->user_uri = $data->feed->entry[$i]->author[0]->uri->_t;
        $v->user_name = $data->feed->entry[$i]->author[0]->name->_t;
        
        $v->description = $data->feed->entry[$i]->media_group->media_description->_t;
        //$v->ratio = data->feed->entry[i]->media_group->yt_aspectRatio["$t"];
        $v->id = $data->feed->entry[$i]->media_group->yt_videoid->_t;
        $v->duration = $data->feed->entry[$i]->media_group->yt_duration->seconds;
        //$v->category = $data->feed->entry[$i]->category->term;
        //$url_op = array();
		//exec("youtube-dl --get-url " . $v->id, $url_op);
		//$v->dl_url = $url_op[0];
		
        $res[] = $v;
	}
    return $res;
}

if($_GET["method"] == "search_videos"){
	$r = yt_search_api($_GET["q"], $_GET["s"], $_GET["max"]);
	echo $_GET['token'] . "(" . json_encode($r) . ")";
}
?>