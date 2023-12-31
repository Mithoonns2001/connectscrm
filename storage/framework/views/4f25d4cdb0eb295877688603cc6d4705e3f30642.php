<?php echo e(Form::model($indicator,array('route' => array('indicator.update', $indicator->id), 'method' => 'PUT'))); ?>

<div class="modal-body">
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <?php echo e(Form::label('branch',__('Branch'),['class'=>'form-label'])); ?>

                <?php echo e(Form::select('branch',$brances,null,array('class'=>'form-control select','required'=>'required'))); ?>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?php echo e(Form::label('department',__('Department'),['class'=>'form-label'])); ?>

                <?php echo e(Form::select('department',$departments,null,array('class'=>'form-control select','required'=>'required','id'=>'department_id'))); ?>

            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <?php echo e(Form::label('designation',__('Designation'),['class'=>'form-label'])); ?>

                <select class="select form-control select2-multiple" id="designation_id" name="designation" data-toggle="select2" data-placeholder="<?php echo e(__('Select Designation ...')); ?>" required>
                </select>
            </div>
        </div>

    </div>

     <?php $__currentLoopData = $performance; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $performances): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <div class="row">
        <div class="col-md-12 mt-3">
            <h6><?php echo e($performances->name); ?></h6>
            <hr class="mt-0">
        </div>
            <?php $__currentLoopData = $performances->types; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $types): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <div class="col-6">
                <?php echo e($types->name); ?>

            </div>
            <div class="col-6">
                <fieldset id='demo1' class="rating">
                    <input class="stars" type="radio" id="technical-5-<?php echo e($types->id); ?>" name="rating[<?php echo e($types->id); ?>]" value="5" <?php echo e((isset($ratings[$types->id]) && $ratings[$types->id] == 5)? 'checked':''); ?>>
                    <label class="full" for="technical-5-<?php echo e($types->id); ?>" title="Awesome - 5 stars"></label>
                    <input class="stars" type="radio" id="technical-4-<?php echo e($types->id); ?>" name="rating[<?php echo e($types->id); ?>]" value="4" <?php echo e((isset($ratings[$types->id]) && $ratings[$types->id] == 4)? 'checked':''); ?>>
                    <label class="full" for="technical-4-<?php echo e($types->id); ?>" title="Pretty good - 4 stars"></label>
                    <input class="stars" type="radio" id="technical-3-<?php echo e($types->id); ?>" name="rating[<?php echo e($types->id); ?>]" value="3" <?php echo e((isset($ratings[$types->id]) && $ratings[$types->id] == 3)? 'checked':''); ?>>
                    <label class="full" for="technical-3-<?php echo e($types->id); ?>" title="Meh - 3 stars"></label>
                    <input class="stars" type="radio" id="technical-2-<?php echo e($types->id); ?>" name="rating[<?php echo e($types->id); ?>]" value="2" <?php echo e((isset($ratings[$types->id]) && $ratings[$types->id] == 2)? 'checked':''); ?>>
                    <label class="full" for="technical-2-<?php echo e($types->id); ?>" title="Kinda bad - 2 stars"></label>
                    <input class="stars" type="radio" id="technical-1-<?php echo e($types->id); ?>" name="rating[<?php echo e($types->id); ?>]" value="1" <?php echo e((isset($ratings[$types->id]) && $ratings[$types->id] == 1)? 'checked':''); ?>>
                    <label class="full" for="technical-1-<?php echo e($types->id); ?>" title="Sucks big time - 1 star"></label>
                </fieldset>
            </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</div>
<div class="modal-footer">
    <input type="button" value="<?php echo e(__('Cancel')); ?>" class="btn btn-light" data-bs-dismiss="modal">
    <input type="submit" value="<?php echo e(__('Update')); ?>" class="btn  btn-primary">
</div>
<?php echo e(Form::close()); ?>


<script type="text/javascript">
    function getDesignation(did) {
        $.ajax({
            url: '<?php echo e(route('employee.json')); ?>',
            type: 'POST',
            data: {
                "department_id": did, "_token": "<?php echo e(csrf_token()); ?>",
            },
            success: function (data) {
                console.log(data);
                $('#designation_id').empty();
                $('#designation_id').append('<option value="">Select any Designation</option>');
                $.each(data, function (key, value) {
                    var select = '';
                    if (key == '<?php echo e($indicator->designation); ?>') {
                        select = 'selected';
                    }

                    $('#designation_id').append('<option value="' + key + '"  ' + select + '>' + value + '</option>');
                });
            }
        });
    }

    $(document).ready(function () {
        var d_id = $('#department_id').val();
        getDesignation(d_id);
    });

</script>



<?php /**PATH /home/u448490903/domains/srmconnects.com/public_html/connectscrm/resources/views/indicator/edit.blade.php ENDPATH**/ ?>