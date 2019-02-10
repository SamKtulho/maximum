
<form class="form-horizontal">
    <input type="hidden" class="coupon-id" name="coupon-id">
    <div class="form-group">
        <label class="col-sm-2 control-label">Name</label>
        <div class="col-sm-10">
            <p class="form-control-static name"></p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Description</label>
        <div class="col-sm-10">
            <p class="form-control-static description">
            </p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Offer</label>
        <div class="col-sm-10">
            <p class="form-control-static offer_name">
            </p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Active to</label>
        <div class="col-sm-10">
            <p class="form-control-static active_to">
            </p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Type</label>
        <div class="col-sm-10">
            <p class="form-control-static coupon_type">
            </p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Promo code</label>
        <div class="col-sm-10">
            <p class="form-control-static promo_code">
            </p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">URL</label>
        <div class="col-sm-10">
            <p class="form-control-static url">
                <a href=""></a>
            </p>
        </div>
    </div>

    <div class="form-group">
        <label class="col-sm-2 control-label">Image</label>
        <div class="col-sm-10">
            <p class="form-control-static image">
                <img style="-webkit-user-select: none;" src=""\>
            </p>
        </div>
    </div>

    <div class="form-group">
        {!! Form::submit('Done', ['class'=>'btn btn-primary done-button']) !!}
    </div>

</form>


