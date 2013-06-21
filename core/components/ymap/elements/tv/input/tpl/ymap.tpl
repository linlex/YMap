<input type="text" id="tv{$tv->id}" name="tv{$tv->id}" value="{$tv->value}" class="textfield"/>
<form style="margin-top:15px;" id="search_form">
    <input type="text" id="ysearch" name="ysearch" value="" class="textfield"/>
    <input type="submit" id="ysearchbtn"  name="ysearchbtn" value="Найти"/>
</form>
<div id="map{$tv->id}" style="border:1px solid #CCC;margin-top:20px;width:800px;height:400px"></div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.2/jquery.min.js" type="text/javascript"></script>
<script src="http://api-maps.yandex.ru/2.0-stable/?load=package.standard&lang=ru-RU" type="text/javascript"></script>

<script type="text/javascript">
// <![CDATA[
{literal}
function is_array(input){
    return typeof(input) == 'object' && (input instanceof Array);
}

ymaps.ready(init);
var map;

function init(){
    startPosition = $.parseJSON('{/literal}{$tv->value}{literal}');
    if (!is_array(startPosition) || (is_array(startPosition) && startPosition.length != 2)) {
        startPosition = [55.755773, 37.617761];
	$("#tv{/literal}{$tv->id}{literal}").val(JSON.stringify(startPosition));
    }
    map = new ymaps.Map ("map{/literal}{$tv->id}{literal}", {
        center: startPosition,
        zoom: 7,
    });
    map.controls.add(new ymaps.control.MapTools());
    map.controls.add(new ymaps.control.TypeSelector(["yandex#map", "yandex#hybrid", "yandex#satellite", "yandex#publicMap"]));
    map.controls.add(new ymaps.control.ZoomControl());
    placemark = new ymaps.Placemark(startPosition, {}, {draggable: true});
    placemark.events.add("drag", function(e) {
        $("#tv{/literal}{$tv->id}{literal}").val(JSON.stringify(placemark.geometry.getCoordinates()));
    });
    map.geoObjects.add(placemark);
    $("#search_form").submit(function () {
        ymaps.geocode($("#ysearch").val(), {results: 1}).then(
            function (res) {
                if (res.geoObjects.getLength()) {
                    point = res.geoObjects.get(0);
                    placemark.geometry.setCoordinates(point.geometry.getCoordinates());
                    /*currentCordinates = $.parseJSON($(placemark.geometry.getCoordinates()).val());*/
                    $("#tv{/literal}{$tv->id}{literal}").val(JSON.stringify(placemark.geometry.getCoordinates()));
                    map.panTo(point.geometry.getCoordinates(), {flying: true});
                } else {
                    alert("Ничего не найдено!");
                }
            },
            function (error) {
                alert("Возникла ошибка: " + error.message);
            }
        );
        return false;
    });
}

{/literal}
// ]]>
</script>







