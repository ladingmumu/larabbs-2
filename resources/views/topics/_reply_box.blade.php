@include('common.error')
<div class="reply-box">
    <form action="{{ route('replies.store') }}" method="POST" accept-charset="UTF-8">
        <input type="hidden" name="_token" value="{{ csrf_token() }}">
        <input type="hidden" name="topic_id" value="{{ $topic->id }}">
        <div class="form-group">
            <textarea name="content" id="content" rows="3" class="form-control" placeholder="分享你的想法"></textarea>
        </div>
        <button class="btn btn-primary btn-sm" type="submit"><i class="fa fa-share"></i>回复</button>
        <hr>
    </form>
</div>