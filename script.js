console.log('initMap() が実行されました');

document.addEventListener('DOMContentLoaded', () => {

  // 地図の中心座標 (必要であれば変更)
  const center = { lat: 28.7041, lng: 77.1025 }; // デリー

  // 地図のオプション
  const mapOptions = {
      zoom: 12,
      center: center
  };

  // 地図オブジェクトを作成
  const map = new google.maps.Map(document.getElementById('map'), mapOptions);

  spots.forEach(spot => {
    // マーカーの色を設定
    let markerColor = 'red'; // デフォルトは赤
    if (spot.user_id == currentUserId) { // currentUserId を参照
        markerColor = 'blue'; // ログインユーザーのスポットは青
    }

    const marker = new google.maps.Marker({ // マーカーを定数で宣言
        position: { lat: parseFloat(spot.latitude), lng: parseFloat(spot.longitude) },
        map: map,
        title: spot.spot_name,
        icon: {
            path: google.maps.SymbolPath.CIRCLE,
            fillColor: markerColor,
            fillOpacity: 0.8,
            strokeColor: 'white',
            strokeWeight: 2,
            scale: 8
        }
    });

    // マーカークリックイベント (ループ内に移動)
    marker.addListener('click', () => {
      // スポット詳細画面に遷移
      window.location.href = `spot_detail.php?id=${spot.spot_id}`; 
    });
  });

  // 検索ボックスの要素を取得
  const searchBox = document.getElementById('search-box');
  console.log('サーチボックス:', searchBox); 

  // Places Autocomplete を検索ボックスに適用
  const autocomplete = new google.maps.places.Autocomplete(searchBox);
  console.log('autocomplete実行:', autocomplete); 

  // 検索結果が選択された時のイベントリスナー
  let marker; // マーカー変数を宣言

  autocomplete.addListener('place_changed', () => {
      const place = autocomplete.getPlace();
      if (!place.geometry) return;

      // 地図の中心を検索結果の場所に移動
      map.setCenter(place.geometry.location);
      map.setZoom(15); // 必要であればズームレベルを変更

      // 既存のマーカーを削除 (必要であれば)
      if (typeof marker !== 'undefined') { 
          marker.setMap(null); 
      }

      // スポット情報ウィンドウを作成
      const infoWindowContent = `
          <h3>${place.name}</h3>
          <p>${place.formatted_address}</p>
          <a href="spot_register.php?lat=${place.geometry.location.lat()}&lng=${place.geometry.location.lng()}&name=${encodeURIComponent(place.name)}&address=${encodeURIComponent(place.formatted_address)}" class="register-button">お気に入りに登録</a>
      `;

      const infoWindow = new google.maps.InfoWindow({
          content: infoWindowContent
      });

      // 新しいマーカーを作成
      marker = new google.maps.Marker({
          map: map,
          position: place.geometry.location
      });

      infoWindow.open(map, marker);
  });
});