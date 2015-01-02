<form method="get" class="form-money form-horizontal in" role="form" name='YM_in' style="display: none" action="/money/YM_transaction">
    <div class="submit-dialog-captions"><?php print Core::translateToCurrentLocale("Value"); ?>:</div>
    <input type="text" name="value"><br>
    <input type="submit" value="Submit">
</form>

<form method="get" class="form-money form-horizontal" role="form" name='YM_out' style="display: none" action="/money/YM_transaction_o">
    <div class="submit-dialog-captions"><?php print Core::translateToCurrentLocale("Value"); ?>:</div>
    <input type="text" name="value"><br>
    <div class="submit-dialog-captions">To:</div>
    <input type="text" id="yp" name="yar_path"><br>
    <input class="btn" type="submit" value="Submit">
</form>