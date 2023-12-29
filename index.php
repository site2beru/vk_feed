<div id='posts-container'>
<?
function ru_month($timestamp) {

    $day = date('j', $timestamp);

    $month_arr = array(
        1 => 'января',
        2 => 'февраля',
        3 => 'марта',
        4 => 'апреля',
        5 => 'мая',
        6 => 'июня',
        7 => 'июля',
        8 => 'августа',
        9 => 'сентября',
        10 => 'октября',
        11 => 'ноября',
        12 => 'декабря',
    );

    $month = $month_arr[date('n', $timestamp)];

    $year = date('Y', $timestamp);
    $hour = date('H', $timestamp);
    $minute = date('i', $timestamp);

    return $day . ' ' . $month . ' ' . $year . ' в ' . $hour . ':' . $minute;
}

$access_token = 'efad1089efad1089efad108938ecbb1fc4eefadefad10898a9bd12d05a2127be3df3dbe';
$group_id = 55100378;
$url = "https://api.vk.com/method/wall.get?owner_id=-{$group_id}&access_token={$access_token}&v=5.131&count=10";

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$res = curl_exec($ch);
curl_close($ch);

$response = json_decode($res, true);

if (isset($response['response']['items'])) {
    foreach ($response['response']['items'] as $post) {
        $formattedText = nl2br(htmlspecialchars($post['text']));
        $postDate = ru_month($post['date']); 
        echo "<p><strong>{$postDate}</strong></p>";
        echo "<pre>{$formattedText}</pre><br>";
        
        // Если в посте фото, публикуем фото
        
        if (isset($post['attachments'])) {
            foreach ($post['attachments'] as $attachment) {
                if ($attachment['type'] == 'photo') {
                    $photo = end($attachment['photo']['sizes']);
                    echo "<img src='{$photo['url']}'><br>";
                    echo "";
                }
                // Если в посте статья, публикуем ссылку на нее
                
                else if ($attachment['type'] == 'link') {
                    $attach = $attachment['link'];
                    echo "<a href='{$attach['url']}' target='_blank'>ССЫЛКА</a><br>";
                    echo "";
                }
            }
        }
    }
} else {
    echo 'Error: ' . $response['error']['error_msg'];
}
?>
</div>
<!-- Еще 10 постов -->
<button id="load-more-button">Еще</button>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script>
    var offset = 10; // сколько постов уже выведено изначально
    var count = 10; // сколько постов подгружать по кнопке
    var access_token = 'efad1089efad1089efad108938ecbb1fc4eefadefad10898a9bd12d05a2127be3df3dbe';
    var group_id = 55100378;
    
    // Дата в человеческом формате
    
    function ru_month(timestamp) {
    const monthNames = ["января", "февраля", "марта", "апреля", "мая", "июня", "июля", "августа", "сентября", "октября", "ноября", "декабря"];
    let date = new Date(timestamp * 1000);
    let day = ("0" + date.getDate()).slice(-2); // форматируем день (01-31)
    let monthIndex = date.getMonth(); // получаем индекс месяца (0-11)
    let year = date.getFullYear(); // получаем год
    let hours = ("0" + date.getHours()).slice(-2); // форматируем часы (00-23)
    let minutes = ("0" + date.getMinutes()).slice(-2); // форматируем минуты (00-59)
    return day + ' ' + monthNames[monthIndex] + ' ' + year + ' в ' + hours + ':' + minutes;
    

}

    // Подгружаем еще посты
    
    function loadMorePosts() {
        $.ajax({
            url: `https://api.vk.com/method/wall.get?owner_id=-${group_id}&access_token=${access_token}&v=5.131&offset=${offset}&count=${count}`,
            type: "GET",
            dataType: "jsonp",
            success: function(response) {
                // Handle the response and append new posts to the container
                var posts = response.response.items;
                var container = $("#posts-container");
                for (var i = 0; i < posts.length; i++) {
                    // Format and append each post to the container
                    var formattedText = posts[i].text.replace(/&#(\d+);/g, function(match, dec) {
                        return String.fromCharCode(dec);
                    });
                    var postDate = ru_month(posts[i].date);
                    container.append("<p>" + postDate + "</p>");
                    container.append("<pre>" + formattedText + "</pre><br>");
                    
                    if (posts[i].attachments) {
                        posts[i].attachments.forEach(function(attachment) {
                            if (attachment.type == 'photo') {
                                var photoUrl = attachment.photo.sizes[attachment.photo.sizes.length - 1].url;
                                container.append("<img src='" + photoUrl + "'><br>");
                            }
                        });
                    }
                }
                offset += count; // Increase the offset for the next request
            },
            error: function(error) {
                console.log(error);
            }
        });
    }

    // Attach click event handler to the button
    $("#load-more-button").click(function() {
        loadMorePosts();
    });
</script>
<style>
pre {
  width: 100%;
  display: block;
  word-wrap: break-word;
  white-space: pre-wrap;
  line-height: 1em;
}
</style>