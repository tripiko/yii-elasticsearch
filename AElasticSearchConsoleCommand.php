<?php
Yii::import("packages.elasticSearch.*");
/**
 * Provides command line access to console commands
 * @author Charles Pick
 * @author Stratos Gerakakis
 * @package packages.elasticSearch
 */
class AElasticSearchConsoleCommand extends CConsoleCommand {
	public function actionReindex($modelClass) {
		$modelClass::model()->deleteMapping();
		$modelClass::model()->putMapping();
		#$curl = new ACurl;
		#die(((string) $curl->put("http://192.168.1.8:9200/w/",json_encode(array("settings" => array("number_of_shards" => 1))))->exec()));
		$total = $modelClass::model()->count();

		$start = 0;
		$pageSize = 500;
		while($start < $total) {
			$criteria = new CDbCriteria;
			$criteria->limit = $pageSize;
			$criteria->offset = $start;
			foreach($modelClass::model()->findAll($criteria) as $model) {
				echo "Indexing ".$model->primaryKey."...";
				if ($model->index()) {
					echo "Done\n";
				}
				else {
					die("ERROR INDEXING");
				}
			}
			$start += $pageSize;
		}
	}

	public function actionSearch($query) {
		$criteria = new AElasticSearchCriteria();
		$criteria->query()->flt(array(
								"fields" => array("title", "proj_desc"),
								"like_text" => $query
							));
		$criteria->limit = 20;
		print_r($criteria->toArray());
		print_r(Yii::app()->elasticSearch->search("main","Projects",$criteria));
	}
}