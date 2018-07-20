@foreach ($feeds['hits'] as $feed)
    <div class="post">
        <h5 class="profile-header"> <img src="{{$feed['_source']['profile_picture']}}" /> {{$feed['_source']['username']}}</h5>
        <p><img class="img-responsive" src="{{$feed['_source']['post']}}" alt="orange-tree" /></p>
        @if (!empty($feed['_source']['caption']))
            <p>
                <?php $caption = preg_replace('/(?:^|\s)#(\w+)/', ' <a href="tag/$1">#$1</a>', $feed['_source']['caption']['text']);
                print $caption;
                ?>
            </p>
        @endif
        <p>
            {{$feed['_source']['likes_count']}} likes &nbsp; {{$feed['_source']['comments_count']}} Comments
        </p>
        <p>{{\Carbon\Carbon::createFromTimestamp($feed['_source']['created_time'])->diffForHumans() }}</p>
    </div>
@endforeach