console.log('initMap() が実行されました');

function initMap() {
    // 地図の中心座標 (必要であれば変更)
    const center = { lat: 28.7041, lng: 77.1025 }; 
  
    // 地図のオプション
    const mapOptions = {
      zoom: 12, 
      center: center
    };
  
    // 地図オブジェクトを作成
    const map = new google.maps.Map(document.getElementById('map'), mapOptions);
  
    // 地図クリックイベント
    map.addListener('click', (event) => {
      const latitude = event.latLng.lat();
      const longitude = event.latLng.lng();
  
      // 緯度経度を隠しフィールドにセット
      document.getElementById('latitude').value = latitude;
      document.getElementById('longitude').value = longitude;
  
      // マーカーを設置 
      new google.maps.Marker({
        position: { lat: latitude, lng: longitude },
        map: map,
        title: 'クリックした場所'
      });
    });
  } 