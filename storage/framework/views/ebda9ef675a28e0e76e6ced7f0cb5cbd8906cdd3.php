<?php echo e(Form::open(array('url'=>'company-policy','method'=>'post', 'enctype' => "multipart/form-data"))); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <?php echo e(Form::label('branch',__('Branch'),['class'=>'form-label'])); ?>

                <?php echo e(Form::select('branch',$branch,null,array('class'=>'form-control select','required'=>'required'))); ?>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?php echo e(Form::label('title',__('Title'),['class'=>'form-label'])); ?>

                <?php echo e(Form::text('title',null,array('class'=>'form-control','required'=>'required'))); ?>

            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?php echo e(Form::label('description', __('Description'),['class'=>'form-label'])); ?>

                <?php echo e(Form::textarea('description',null, array('class' => 'form-control'))); ?>

            </div>
        </div>
        <div class="col-md-12">
            <?php echo e(Form::label('attachment',__('Attachment'),['class'=>'form-label'])); ?>

            <div class="choose-file form-group">
                <label for="attachment" class="form-label">
                    <input type="file" class="form-control" name="attachment" id="attachment" data-filename="attachment_create">
                    <img id="image" class="mt-3" style="width:25%;"/>

                </label>
                
            </div>
        </div>


    </div>
</div>
<div class="modal-footer">
    <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn  btn-light" data-bs-dismiss="modal">
    <input type="submit" value="<?php echo e(__('Create')); ?>" class="btn  btn-primary">
</div>
<?php echo e(Form::close()); ?>



<script>
    document.getElementById('attachment').onchange = function () {
        var src = URL.createObjectURL(this.files[0])
        document.getElementById('image').src = src
    }
</script>

<?php /**PATH /home/u448490903/domains/srmconnects.com/public_html/connectscrm/resources/views/companyPolicy/create.blade.php ENDPATH**/ ?>