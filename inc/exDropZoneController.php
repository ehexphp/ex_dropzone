<?php
/**
 * Created by PhpStorm.
 * User: samtax
 * Date: 01/12/2018
 * Time: 3:03 PM
 */







class exDropZoneController extends Controller1{

    /**
     * @return bool|string
     */
    static function upload(){
        $model_id = $_REQUEST['model_id'];
        $model_name = $_REQUEST['model_name'];
        /** @var Model1 $model */
        $model = $model_name::withId($model_id);

        // action
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : null;
        $save_option = $_REQUEST['save_option'];

        //Other Requirement are $_REQUEST['file_name'], $_FILES['file']
        //sreturn ResultObject1::falseMessage($_REQUEST);


        // Save to local Storage
        if($save_option === 'local') {
            if ($action === 'delete') {
                if( ($result = $model->deleteFile($_REQUEST['file_name'])) == Model1FileLocator::delete($model, $_REQUEST['file_name']) ) return ResultObject1::trueData($result);
            } else if(isset($_FILES['file'])) {
                if( ($result = $model->uploadFile($_FILES['file'], $_FILES['file']['name'])) && Model1FileLocator::insertUrl($model, $result, $_FILES['file']['name']) ) return ResultObject1::trueData($result);
            }
            return ResultObject1::falseMessage('Local Upload Operation Failed');
        }


        // delete image
        if($save_option === 'imgur') {
            if ($action === 'delete') {
                //$deleteHashUrl = Model1FileLocator::find_inDb($model, $_REQUEST['file_name'], false)->other_url;
                //Url1::cURL($deleteHashUrl);
                $result = $model->deleteFile($_REQUEST['file_name']) == Model1FileLocator::delete($model, $_REQUEST['file_name']);
                return  ResultObject1::trueData($result);
            } else if(isset($_FILES['file'])) {
                if( ($result =  ImgurFileManager::instance()->upload($_FILES['file']) ) && ($result = Model1FileLocator::insertUrl($model, $result->link(), $_FILES['file']['name'], null, $result->deleteLink()) )) return ResultObject1::trueData($result);
            }
            return ResultObject1::falseMessage('Imgur Upload Operation Failed, Connectivity Problem');
        }

        // upload
        return ResultObject1::falseMessage("No Action or File to upload...");
    }
}