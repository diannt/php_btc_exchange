<div class="modal-dialog" style="width:300px;">
    <div class="modal-content">
        <div class="modal-header" style="text-align: center">
            <?php print Core::translateToCurrentLocale('Loading...'); ?>
        </div>
        <div class="modal-body">
            <div style="height:100px">
                <span id="modalSpinner" style="position: absolute;display: block;top: 50%;left: 50%;"></span>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $.fn.ready(function(){
        PopupLoader.show();
    });
</script>