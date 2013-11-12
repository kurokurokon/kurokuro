<?php

class ApiController extends \BaseController {
	public function getList(){
		$limit = Input::get("limit");
		$offset = Input::get("offset");
		if (empty($limit) ||
			preg_match('/^\d+$/', $limit)){
			$limit = 20;
		}
		if (empty($offset) ||
			preg_match('/^\d+$/', $offset)){
			$offset = 0;
		}
		$realOffset = $limit * $offset;


		$retval = array(
			"status"=>"ok",
			"datas"=>array(
				"total_num"=>0,
				"datas"=>array()
			)
		);

		$retval["datas"]["total_num"] = DB::table("res_main")
			->count();

		$resourceRecords = DB::table("res_main")
			->skip($realOffset)
			->take($limit)
			->get();
		for ($i=0;$i<sizeof($resourceRecords);++$i){
			$resourceRecordElement = $resourceRecords[$i];

			$contentRecord = DB::table("content_main")
				->where("id","=",$resourceRecordElement->content_id)
				->first();
			$content = "";
			if ($contentRecord != NULL){
				$content = $contentRecord->data;
			}
			$coverRecord = DB::table("cover_main")
				->where("id","=",$resourceRecordElement->cover_id)
				->first();
			$coverURL = "";
			if ($coverRecord != NULL){
				$coverURL = $coverRecord->url;
			}
			Session::put("r_id",$resourceRecordElement->id);
			$tagRecord = DB::table("tags_main")
				->whereIn("id",function($query){
					$query->select("t_id")
						->from("res_tags")
						->whereRaw("r_id = ".Session::get("r_id"));
				})
				->get();

			$downloadLinks = DB::table("datas_main")
				->whereIn("id",function ($query){
					$query->select("d_id")
						->from("res_datas")
						->whereRaw("r_id = ".Session::get("r_id"));
				})
				->get();
			Session::forget("r_id");

			$resouceElement = array();
			$resouceElement["id"] = $resourceRecordElement->id;
			$resouceElement["name"] = $resourceRecordElement->name;
			$resouceElement["add_time"] = $resourceRecordElement->add_time;
			$resouceElement["update_time"] = $resourceRecordElement->update_time;
			$resouceElement["size"] = $resourceRecordElement->size;
			$resouceElement["content"] = $content;
			$resouceElement["cover"] = $coverURL;
			$resouceElement["tag"] = $tagRecord;
			$resouceElement["res"] = $downloadLinks;

			$retval["datas"]["datas"][$i] = $resouceElement;
		}

		$callback = Input::get('callback');
		if (empty($callback)){
			$callback = "laravel_".time();
		}
		return Response::json($retval)
			->setCallback($callback);

	}

	public function getTags(){
		$limit = Input::get("limit");
		if (empty($limit) ||
			preg_match('/^\d+$/', $limit) == 0){
			$limit = 10;
		}

		$tagRecords = DB::table("tags_main")
			->take($limit)
			->get();
		$retval = array();
		for ($i=0;$i<sizeof($tagRecords);++$i){
			$retval[$i] = array(
				"id"=>$tagRecords[$i]->id,
				"name"=>$tagRecords[$i]->name,
				"count"=>DB::table("res_tags")
					->where("t_id","=",$tagRecords[$i]->id)
					->count()
			);
		}
		$callback = Input::get('callback');
		if (empty($callback)){
			$callback = "laravel_".time();
		}
		return Response::json($retval)
			->setCallback($callback);
	}

	public function getSearch(){
		$retval = array(
			"status"=>"ok",
			"datas"=>array(
				"total_num"=>0,
				"datas"=>array()
			)
		);

		$retval["datas"]["total_num"] = 0;


		$keywords = Input::get("keywords");
		$tagID = Input::get("tagid");

		if (empty($limit) ||
			preg_match('/^\d+$/', $limit)){
			$limit = 20;
		}
		if (empty($offset) ||
			preg_match('/^\d+$/', $offset)){
			$offset = 0;
		}
		$realOffset = $limit * $offset;

		$resourceRecords = NULL;
		$resourceIDs = array();
		$resourceIDValueSet = array(0);

		if (!empty($keywords)){
			$retval["datas"]["total_num"] = DB::table("res_main")
				->where("name","LIKE",'%'.$keywords.'%')
				->count();
			$resourceRecords = DB::table("res_main")
				->where("name","LIKE",'%'.$keywords.'%')
				->skip($realOffset)
				->take($limit)
				->get();
		}else {
			$retval["datas"]["total_num"] = DB::table("res_tags")
				->where("t_id","=",$tagID)
				->count();
			$resourceIDs = DB::table("res_tags")
				->where("t_id","=",$tagID)
				->skip($realOffset)
				->take($limit)
				->get();
			for ($i=0;$i<sizeof($resourceIDs);++$i){
				$resourceIDValueSet[$i] = $resourceIDs[$i]->r_id;
			}
			$resourceRecords = DB::table("res_main")
				->whereIn("id",$resourceIDValueSet)
				->get();
		}

		for ($i=0;$i<sizeof($resourceRecords);++$i){
			$resourceRecordElement = $resourceRecords[$i];

			$contentRecord = DB::table("content_main")
				->where("id","=",$resourceRecordElement->content_id)
				->first();
			$content = "";
			if ($contentRecord != NULL){
				$content = $contentRecord->data;
			}
			$coverRecord = DB::table("cover_main")
				->where("id","=",$resourceRecordElement->cover_id)
				->first();
			$coverURL = "";
			if ($coverRecord != NULL){
				$coverURL = $coverRecord->url;
			}
			Session::put("r_id",$resourceRecordElement->id);
			$tagRecord = DB::table("tags_main")
				->whereIn("id",function($query){
					$query->select("t_id")
						->from("res_tags")
						->whereRaw("r_id = ".Session::get("r_id"));
				})
				->get();

			$downloadLinks = DB::table("datas_main")
				->whereIn("id",function ($query){
					$query->select("d_id")
						->from("res_datas")
						->whereRaw("r_id = ".Session::get("r_id"));
				})
				->get();
			Session::forget("r_id");

			$resouceElement = array();
			$resouceElement["id"] = $resourceRecordElement->id;
			$resouceElement["name"] = $resourceRecordElement->name;
			$resouceElement["add_time"] = $resourceRecordElement->add_time;
			$resouceElement["update_time"] = $resourceRecordElement->update_time;
			$resouceElement["size"] = $resourceRecordElement->size;
			$resouceElement["content"] = $content;
			$resouceElement["cover"] = $coverURL;
			$resouceElement["tag"] = $tagRecord;
			$resouceElement["res"] = $downloadLinks;

			$retval["datas"]["datas"][$i] = $resouceElement;
		}
		if (empty($callback)){
			$callback = "laravel_".time();
		}
		return Response::json($retval)
			->setCallback($callback);
	}
}