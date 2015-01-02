<form role="form" id="feedback-form" name="feedback-form" action="/usr/submitFeedback" class="col-xs-7">
    <div class="form-group">
        <label class="greenMark" for="InputEmail"><?php print Core::translateToCurrentLocale('Type your e-mail'); ?></label>
        <input type="email" class="form-control" id="InputEmail" placeholder="<?php print Core::translateToCurrentLocale('Type your e-mail'); ?>">
    </div>
    <div class="form-group">
        <div>
            <label class="greenMark"><?php print Core::translateToCurrentLocale('Type your trouble'); ?>:</label>
        </div>
        <label class="radio-inline">
            <input type="radio" name="trouble-type" id="inlineRadio1" value="breaking"> <?php print Core::translateToCurrentLocale('Breaking an account'); ?>
        </label>
        <label class="radio-inline">
            <input type="radio" name="trouble-type" id="inlineRadio2" value="bug"> <?php print Core::translateToCurrentLocale('Bug'); ?>
        </label>
        <label class="radio-inline">
            <input type="radio" name="trouble-type" id="inlineRadio3" value="info"> <?php print Core::translateToCurrentLocale('Info'); ?>
        </label>
    </div>
    <div class="form-group">
        <label class="greenMark" for="textArea"><?php print Core::translateToCurrentLocale('Please, describe your trouble'); ?>:</label>
        <textarea id="textArea" name="trouble" class="form-control" rows="3"></textarea>
    </div>
    <div class="form-group">
        <label class="greenMark" for="InputCaptcha"><?php print Core::translateToCurrentLocale('Please type a captcha'); ?>:</label>
        <img src="/api/captcha"><input type="text" name="captcha" class="form-control" id="InputCaptcha" placeholder="<?php print Core::translateToCurrentLocale('Captcha'); ?>:">
    </div>
    <div class="form-group">
        <input type="submit" class="btn btn-blue" value="<?php print Core::translateToCurrentLocale('Send a report'); ?>">
    </div>
  </form>
<div id="after-form-block">
    <?php print Core::translateToCurrentLocale('Thank you for you feedback!'); ?>
</div>
<script>
    $.fn.ready(function(){
        $('#feedback-form').submit(function(e){
            var url = "/usr/submitFeedback?" + $(this).serialize();

            $.ajax({
                type: "GET",
                url: url,
                dataType: "json",
                success: function (response) {
                    if(response.success == '0')
                    {
                        alert(response.error);
                        location.reload();
                    }
                    else
                    {
                        $('#feedback-form').remove();
                        $('#after-form-block').show();
                    }

                },
                error: function()
                {
                    location.reload();
                }
            });

            e.preventDefault();
        });

    });
</script>