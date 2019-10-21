<?php
/**
 * Created by PhpStorm.
 * User: samtax
 * Date: 01/12/2018
 * Time: 3:03 PM
 */







class exDropZone{

    /**
     * Save image to FileSystem or Upload to Imgur, and Save Link to Model1FileLocator
     * Model1FileLocator Required
     *
     * @param null $model
     * @param bool $uploadToImgur
     * @param array $hideByFilePath
     * @param int $maxFileCount
     * @param int $maxFileSize
     * @param string $allowedExtension
     * @param string $title
     * @return string
     * 
     */
    static function imagesDragDropUploadBox($model = null, $uploadToImgur = false,  $hideByFilePath = [], $maxFileCount = 6, $maxFileSize = 10, $allowedExtension = "image/*,application/pdf,.doc,.docx,.xls,.xlsx,.csv,.tsv,.ppt,.pptx,.pages,.odt,.rtf", $title = '<i class="fa fa-upload"></i> UPLOAD YOUR FILES'){
        ob_start();
            if(!$model) throw new Exception(Console1::println(static::class."::imagesDragDropUploadBox(...) Requires Model1 Instance"));
            $existingDbImage = Model1FileLocator::selectAll_fromDb($model, false);
            $dz_upload_controller = Form1::callApi("exDropZoneController::upload()?token=".token()."&model_name=".$model->getModelClassName()."&model_id=$model->id&save_option=".($uploadToImgur? 'imgur': 'local'));
            $dz_delete_controller = $dz_upload_controller;
        ?>


        <!-- CSS -->
        <link href="<?= current_plugin_asset() ?>/dropzone.css" rel="stylesheet" type="text/css">
        <!-- Script -->
        <script src='<?= current_plugin_asset() ?>/jquery-3.2.1.min.js'></script>
        <script src="<?= current_plugin_asset() ?>/dropzone.js" type="text/javascript"></script>








        <style>
            .ex_dropzone_container{ margin: 0 auto; width: 100%; }
            .dz-message{ text-align: center; font-size: 28px; }
            .dropzone{ border:silver 1px solid; border-radius: 20px; }
        </style>
        <div class="row ex_dropzone_container" style="<?= HtmlStyle1::getShadow2x() ?>; padding:10px;">
            <div class='col-12 ex_dropzone_content' style="overflow: hidden">

                <h4 class="section-sub-title"><span><?php echo $title ?>  </span></h4>
                <p style="font-size: 12px; overflow: auto">
                    <span >Only <?php echo $allowedExtension ?> files types are supported. Maximum file Count is <?php echo $maxFileCount ?>, And Maximum file size is <?php echo $maxFileSize ?>MB.</span>
                </p>
                <form class="dropzone" id="myAwesomeDropzone">
                    <!-- Existing Images From DB-->
                    <style>.exists-image{ background: #ff4661; color:white; text-align: center; border-radius: 10px; float:left; margin-bottom:10px; width:100px;}</style>
                    <?php $loadedImages = [];
                    foreach($existingDbImage as $imageUrl) {
                        if(!in_array($imageUrl['file_url'], $hideByFilePath)) {
                            $loadedImages[] = '<div style="float:left; padding:10px; margin:0 auto;"><div class="exists-image">'.HtmlWidget1::fileDeleteBox($imageUrl['id'], exUrl1::urlToPath($imageUrl['file_url']), $imageUrl['file_url'], 'height:80px;width:100%;', 'Delete')."</div></div>";
                        }
                    }
                    $loadedImagesTotal = count($loadedImages); ?>

                    <!-- List Existing Files-->
                    <?php if($loadedImagesTotal > 0){ ?>
                        <div style="clear:both;"></div>
                        <h4 class="section-sub-title"><span>Existing</span> Files (<span><?php echo $loadedImagesTotal  ?></span>)</h4>
                        <div class="row"><?php echo implode('', $loadedImages); ?></div><div style="clear:both"></div>
                    <?php } ?>
                </form>


                <div class="row" style="margin-top:10px;">
                    <div class="col-md-12">
                        <!--  Re-Save All Files in FileSystem to DB-->
                        <?php if(!$uploadToImgur){ ?>
                            <script>
                                function <?= $model->getModelClassName().'_reset()' ?> {
                                    Popup1.confirmAjax('Reset all <?= $model->getModelClassName() ?> Files?', "This will re-save all files to database for easy access, Press yes to continue.", "<?= Form1::callApi(exApiController1::class, 'fileLocatorReSaveLocalFilesToDb()?model_name='.$model->getModelClassName()."&model_id=$model->id&token=".token()) ?>", function(data){
                                        if(data) { Popup1.alert('Files Re-Saved!', '', 'success'); }
                                        else  Popup1.alert('Action failed', 'error ['+data+']', 'error');
                                    })
                                }
                            </script>
                            <a class="btn btn-primary" href="javascript:void(0)" onclick="<?= $model->getModelClassName().'_reset()' ?>"><i class="fa fa-refresh" aria-hidden="true"></i> Refresh</a> &nbsp;&nbsp;
                        <?php } ?>


                        <!--  Delete ALl Files in FileSystem and DB-->
                        <?php if($loadedImagesTotal > 0){ ?>
                            <script>
                                function <?= $model->getModelClassName().'_delete_files()' ?> {
                                    Popup1.confirmAjax('Delete all <?= $model->getModelClassName() ?> Files?', "This will delete all files and this Action Cannot be undo. Press yes to continue.", "<?= Form1::callApi(exApiController1::class, 'fileLocatorDeleteAllFiles()?model_name='.$model->getModelClassName()."&model_id=$model->id&token=".token()) ?>", function(data){
                                        if(data) { Popup1.alert('Files Deleted!', '', 'success'); }
                                        else  Popup1.alert('Action failed', 'error ['+data+']', 'error');
                                    })
                                }
                            </script>
                            <a class="btn btn-danger" href="javascript:void(0)" onclick="<?= $model->getModelClassName().'_delete_files()' ?>"><i class="fa fa-trash" aria-hidden="true"></i> Delete All</a>
                        <?php } ?>
                        <span class="label label-info" id="ex_dropzone_status">Status : OK</span>
                    </div>
                </div>

            </div>
        </div>





        <script type='text/javascript'>
            //$(function(){

                Dropzone.autoDiscover = false;
                $(".dropzone").dropzone({
                    url: "<?php echo $dz_upload_controller ?>",
                    maxFiles: "<?= ($maxFileCount - $loadedImagesTotal) ?>",
                    maxFilesize: "<?= $maxFileSize ?>",
                    acceptedFiles: "<?= $allowedExtension ?>",
                    clickable: true,

                    addRemoveLinks: true,
                    success: function(file, response){
                        response = JSON.parse(response);
                        if(!response.status) { Popup1.alert('Failed to Upload', response.message); $('#ex_dropzone_status').css({'class':'label label-danger'}).text(file.name.toUpperCase() + ' Failed to upload!'); }
                        else{ file.path = response.message; $('#ex_dropzone_status').css({'class':'label label-success'}).text(file.name.toUpperCase() + ' Uploaded Successfully!'); }
                    },
                    removedfile: function(file) {
                        $.ajax({
                            type: 'POST',
                            url: '<?= $dz_delete_controller ?>',
                            data: {file_name: file.name, file_path: file.path, action:'delete'}
                        }).done(function(response){
                            response = JSON.parse(response);
                            if(response.status) Popup1.alert('File Deleted', file.name.toUpperCase() + ' delete successfully', 'success');
                        else Popup1.alert('Failed to Delete File', response.message, 'error');
                        });
                        var _ref;
                        return (_ref = file.previewElement) != null ? _ref.parentNode.removeChild(file.previewElement) : void 0;
                    }
                });

            //});
        </script>
        <?php
        return ob_get_clean();
    }
}