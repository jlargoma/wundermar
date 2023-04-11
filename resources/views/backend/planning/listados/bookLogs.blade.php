<div  id="chats">
  <a href="#"  id="loadchatboxMore" data-date="{{$date}}">ver anteriores..</a>
  <div class="info-date"><b>{{getMonthsSpanish($month)}} {{$year}}</b></div>
  @if($allLogs)
  @foreach($allLogs as $item)
  <div class="chat-item">
    @if(get_class($item) == 'App\MailsLogs')
      <div class="">
        <div class="chat-text">
          {{ $item->subject }}
          <a href="#" class="see_more_mail" data-id="{{$item->id}}"><i class="fa fa-eye"></i></a>
        </div>
        <div class="chat-date">{{date('d.m.Y H:i',strtotime($item->time_msg))}}</div>
      </div>
    @else
      @if($item->user_id)
      @if($item->action == 'change_status')
      <div class="status">
        <div class="chat-text">{{$item->subject}} 
          @if(isset($roomLst[$item->room_id]))<span> {{$roomLst[$item->room_id]}}</span> @endif
        </div>
        <div class="chat-user">
          @if(isset($userLst[$item->user_id])) 
            {{$userLst[$item->user_id]}} 
          @else
            Admin
          @endif
        </div>
        <div class="chat-date">{{$item->created_at->format('d.m.Y H:i')}}</div>
      </div>
      @else
      <div class="admin">
        <div class="chat-text">
          @if($item->action == 'user_mail_response')
          {{ $item->content }}
          @else
          {{$item->subject}}
          <a href="#" class="see_more" data-id="{{$item->id}}">ver mas</a>
          @endif
          <a href="#" class="see_more" data-id="{{$item->id}}">
            <i class="fa fa-eye"></i>
          </a>
        </div>
        <div class="chat-user">
          @if(isset($userLst[$item->user_id])) 
            {{$userLst[$item->user_id]}} 
          @else
            Admin
          @endif
        </div>
        <div class="chat-date">{{$item->created_at->format('d.m.Y H:i')}}</div>
      </div>
      @endif
      @else
      <div class="user">
        <div class="chat-text">
          {{ $item->content }}
          <a href="#" class="see_more" data-id="{{$item->id}}"><i class="fa fa-eye"></i></a>
        </div>
        <div class="chat-date">{{$item->created_at->format('d.m.Y H:i')}}</div>
      </div>
      @endif
  @endif
  </div>
  @endforeach
@endif
</div>