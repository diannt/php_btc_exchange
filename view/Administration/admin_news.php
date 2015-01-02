<h4><span id="addNewNewsButton">Add new news</span></h4>
<div class="addNewsBlock">
    <div class="closeBlock">
    <form name="news" method="post" action="/admin/addNews">
        Title (English):<br>
        <input type="text" name="titleEN"><br>
        Full text (English):<br>
        <textarea name="fullEN"></textarea><br><br>

        Title (Russian):<br>
        <input type="text" name="titleRU"><br>
        Full text (Russian):<br>
        <textarea name="fullRU"></textarea><br><br>

        Title (Spanish):<br>
        <input type="text" name="titleES"><br>
        Full text (Spanish):<br>
        <textarea name="fullES"></textarea><br><br>
        <input type="submit" value="Add">
    </form>
</div>
<script>
    $.fn.ready(function(){
        $('#addNewNewsButton').click(function(){
            if($(".addNewsBlock").css('display') == 'none')
                $(".addNewsBlock").slideDown();
            else
                $(".addNewsBlock").slideUp();
        })
    });
</script>