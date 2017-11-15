<div class="comment-post clearfix">
    <form id="newComment" class="newComment" method="post">
        {{ csrf_field() }}

        <textarea class="post-area" name="comment" id="comment" cols="" rows="2" @if (Auth::guest()) disabled="disabled" @endif placeholder="Залишити коментар..."></textarea>

        <input id="comment_object_id" type="hidden" name="object_id" value="">

        <div class="flex-area">
            <div class="cosial-block">
                <p class="soc-head">УВІЙТИ ЗА ДОПОМОГОЮ</p>
                <div class="soc-icons" id="socialAuth">
                    <a class="socialAuth" href="{{ url('redirect/disqus') }}"><img class="disq-ico soc-ico"
                                                                                   src="{{ asset('img/disq-ico.png') }}" alt=""></a>
                    <a class="socialAuth" href="{{ url('redirect/facebook') }}"><img class="fb-ico soc-ico"
                                                                                     src="{{ asset('img/fb-ico.png') }}" alt=""></a>
                    <a class="socialAuth" href="{{ url('redirect/twitter') }}"><img class="twitt-ico soc-ico"
                                                                                    src="{{ asset('img/twitt-ico.png') }}" alt=""></a>
                    <a class="socialAuth" href="{{ url('redirect/google/')}}"><img class="goog-ico soc-ico"
                                                                                   src="{{ asset('img/google-ico.png') }}" alt=""></a>
                </div>
            </div>

            <div class="submit-block">
                <button type="button" value="Надіслати" class="comment-button submit-btn @if(Auth::guest()) disabled @endif">Надіслати</button>
            </div>
        </div>
    </form>
</div>