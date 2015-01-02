<div class="modal-dialog">
    <div class="modal-content signin-block">
        <div class="modal-header regTitle">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <?php print Core::translateToCurrentLocale('Register'); ?>
        </div>
        <div class="modal-body">
            <form id="registerform" role="form">
                <div class="form-group">
                    <input id="email" type="text" name="email" placeholder="E-mail">
                </div>
                <div class="form-group">
                    <input id="pass1" type="password" name="password" placeholder="Password">
                </div>
                <div class="form-group">
                    <input id="pass2" type="password" onkeyup="checkPass();" placeholder="Repeat Password">
                </div>
                <div class="form-group">
                    <input id="captcha" type="text" name="captcha" placeholder="Captcha" style="width: 141px;"><img id="captchaImg" style="width: 99px; height: 40px; margin-top: 18px;" src='/api/captcha'/>
                </div>
                <div class="form-group">
                    <label>
                        <input type="checkbox"> <?php print Core::translateToCurrentLocale('I agree with'); ?> <a href="#" class="greenMark"><?php print Core::translateToCurrentLocale('rules of the Bitmonex');?></a>
                    </label>
                </div>
                <div class="form-group">
                    <button id="submit" type="button" class="btn btn-primary btn-lg"><?php print Core::translateToCurrentLocale('REGISTER'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<script type="text/javascript">
    $.fn.ready(function(){
        $("#registerform #submit").click(function(event){
            event.preventDefault();
            send();
        });
    });
    function checkPass()
    {
        var success = function(){
            $("#submit").attr('disabled','disabled');
        };
        var error = function(){
            if ($("#pass1").val()!="")
            $("#submit").removeAttr('disabled');
        };

        var method = ($("#pass1").val()!=$("#pass2").val() ? success : error);
        method();
    }
    function send()
    {
        var formid = "#registerform";
        var url = "usr/register";
        var data = $(formid).serialize();
        Ajax.exec(url, data, function(result)
        {
            var success = function(){
                alert("<?php print Core::translateToCurrentLocale('You have successfully registered! Please, enter into your email to confirm'); ?>.");
                hideLoader();
            };
            var error = function(){
                alert(result.error);
                $("#captchaImg").attr("src","/api/captcha");
                $("#captcha").val("");
            };

            var method = (result.success ? success : error);
            method();
        });
    }
</script>