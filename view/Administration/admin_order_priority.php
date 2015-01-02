<div class="wallets-block table-responsive">
    <?php if(isset($data['priority'])): ?>
        <form action="/admin/saveOrderPriority" method="post">
            <pre>
        <div id="priorityBlock1" style="margin: 10px;">
            <div style="height:50px; width: 100px; background-color: #f0f0f0; border: 1px solid #000000;"></div>
            From: <input type="text" name="from1" value="<?php print $data['priority'][0]['from']; ?>"><br>
            To:   <input type="text" name="to1" value="<?php print $data['priority'][0]['to']; ?>"><br>
        </div>
        <div id="priorityBlock2" style="margin: 10px;">
            <div style="height:50px; width: 100px; background-color: #dddddd; border: 1px solid #000000;"></div>
            From: <input type="text" name="from2" value="<?php print $data['priority'][1]['from']; ?>"><br>
            To:   <input type="text" name="to2" value="<?php print $data['priority'][1]['to']; ?>"><br>
        </div>
        <div id="priorityBlock3" style="margin: 10px;">
            <div style="height:50px; width: 100px; background-color: #aaaaaa; border: 1px solid #000000;"></div>
            From: <input type="text" name="from3" value="<?php print $data['priority'][2]['from']; ?>"><br>
            To:   <input type="text" name="to3" value="<?php print $data['priority'][2]['to']; ?>"><br>
        </div>
        <div id="priorityBlock4" style="margin: 10px;">
            <div style="height:50px; width: 100px; background-color: #666666; border: 1px solid #000000;"></div>
            From: <input type="text" name="from4" value="<?php print $data['priority'][3]['from']; ?>"><br>
            To:   <input type="text" name="to4" value="<?php print $data['priority'][3]['to']; ?>"><br>
        </div></pre>
            <input type="submit" value="Save settings">
        </form>
    <? endif; ?>
</div>

