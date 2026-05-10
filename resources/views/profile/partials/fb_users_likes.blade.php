<section>
  <header>
    <h2 class="text-lg font-medium text-gray-900">
      {{ __('按過讚的粉絲頁面') }}
    </h2>
  </header>



  <div>
    <table id="fb_user_likes">
      <thead>
        <tr>
          <th>名稱</th>
          <th>按讚時間</th>
        </tr>
      </thead>
      <tbody>
        @foreach( $FB_users_like_data as $key => $FB_users_like_data_value )

        @if( $key >= 9 )
        
        @break
        @endif

        <tr>
          <td>{{ $FB_users_like_data_value['name'] ?? '' }}</td>
          <td>{{ $FB_users_like_data_value['created_time'] ?? '' }}</td>
        </tr>

        @endforeach

      </tbody>
    </table>
  </div>

</section>

   <style>


        #fb_user_likes {
            width: 100%;
            border-collapse: collapse;
        }

        #fb_user_likes th,
        #fb_user_likes td {
            border: 1px solid #000;
            padding: 12px;
            text-align: center;
        }

        #fb_user_likes th {
            background-color: #f2f2f2;
        }

        #fb_user_likes tr:hover {
            background-color: #fafafa;
        }
    </style>