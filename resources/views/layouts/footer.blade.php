<!-- Footer -->
<footer>
    <div class="footer-top">
        <div class="container">
            <div class="row">
                <div class="col-md-3 footer-about wow fadeInUp">
                    <h1>BBTS</h1>
                    <p>
                        {{ __('footer.info_1') }}
                    </p>
                    <p>
                        {{ __('footer.info_2') }}
                    </p>
                </div>
                <div class="col-md-4 offset-md-1 footer-contact wow fadeInDown">
                    <h3>{{ __('footer.contacts') }}</h3>
                    <p>Email: <a href="mailto:vanguardkras@gmail.com">vanguardkras@gmail.com</a></p>
                    <p></i> Telegram: <a href="https://teleg.run/vanguardkras">vanguardkras</a></p>
                </div>
                <div class="col-md-4 footer-links wow fadeInUp">
                    <div class="row">
                        <div class="col">
                            <h3>{{ __('footer.links') }}</h3>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <p><a href="/">{{ __('footer.main') }}</a></p>
                            <p><a href="/teams">{{ __('footer.teams') }}</a></p>
                            <p><a href="/profile">{{ __('footer.profile') }}</a></p>
                        </div>
                        <div class="col-md-6">
                            <form method="POST" action="https://money.yandex.ru/quickpay/confirm.xml">
                                <input type="hidden" name="receiver" value="410015697221281">
                                <input type="hidden" name="quickpay-form" value="donate">
                                <input type="hidden" name="targets" value="Support BBTS project">
                                <input type="hidden" name="need-fio" value="false">
                                <input type="hidden" name="need-email" value="false">
                                <input type="hidden" name="need-phone" value="false">
                                <input type="hidden" name="need-address" value="false">
                                <input type="hidden" name="paymentType" value="AC">
                                @if (app()->getLocale() === 'ru')
                                    <div class="input-group">
                                        <input type="number" name="sum" value="100" class="form-control">
                                        <div class="input-group-append">
                                            <span class="input-group-text">руб</span>
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" name="sum" value="200">
                                @endif
                                <button class="w-100 btn btn-success" type="submit">{{ __('footer.support') }}</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <div class="row">
                <div class="col-md-6 footer-copyright">
                    {{ date('Y', time()) }} &copy; Max Shaian
                </div>
            </div>
        </div>
    </div>
</footer>
