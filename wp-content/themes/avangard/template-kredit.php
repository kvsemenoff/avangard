<?php
/**
 * Template name: Кредит
 *
 * @package avangard
 */

get_header(); ?>

<?php while ( have_posts() ) : the_post(); ?>

    <div class="content_top">
        <div class="wrap">
            <h1 class="entry-title"><?php the_title(); ?></h1>
        </div><!-- .wrap -->
        <div class="content_top_bottom">
            <div class="wrap">
                <ul class="parent">
                    <li>
                        <span>Новая услуга на рынке электронной коммерции – покупка товара в кредит, не выходя из дома!</span>
                        <ul class="child">
                            <li>Без справок, имея только паспорт</li>
                            <li>Без похода в банк</li>
                            <li>Одобрение в течение часа</li>
                            <li>Сумма до 1 миллиона рублей</li>
                            <li>Действительно, не выходя из дома</li>
                            <li>Возможность бесплатного досрочного погашения </li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </div><!-- .content_top -->

    <div class="content_middle">
        <div class="wrap">
            <h3>КАК КУПИТЬ В КРЕДИТ?</h3>
            <ul class="parent">
                <li class="parent-item-1">
                    <span>Я выбрал товар в интернет-магазине и хочу приобрести его в кредит. Что делать?</span>
                    <ul class="child">
                        <li>Согласуйте стоимость товара с менеджером интернет-магазина по телефону <b>(495) 357-13-00</b> доб.<b>134, 133</b>;</li>
                        <li>Нажмите кнопку «Купить в кредит»;</li>
                        <li>Заполните кредитную заявку и нажмите кнопку «Отправить». </li>
                    </ul>
                </li>
                <li class="parent-item-2">
                    <span>Заявку заполнили. А дальше?</span>
                    <ul class="child">
                        <li>В течение часа кредитный менеджер свяжется с Вами для уточнения паспортных данных.</li>
                        <li>Затем заявка будет рассмотрена банками-партнерами.</li>
                        <li>О принятом решении Вам сообщат до конца дня (а, скорее всего, – уже через пару часов).</li>
                    </ul>
                </li>
                <li class="parent-item-3">
                    <span>Банк одобрил мою заявку. Каков план?</span>
                    <ul class="child">
                        <li>Кредитный менеджер позвонит, чтобы согласовать удобное для Вас время и место встречи для подписания платежных документов.</li>
                        <li>Теперь Вам остается внести первоначальный взнос – от 10% и ждать доставки товара.</li>
                    </ul>
                </li>
            </ul>
            <?php $args = array(
                0 => array('MODEL' => 'divan', 'COUNT' => '1', 'PRICE' => '0')
            ); ?>
            <div class="credit_btn"><a href="javascript:;" onclick='retailcreditdialog(<?php echo json_encode($args); ?>,2);'>Купить в кредит</a></div>
        </div>
    </div>

    <div class="content_bottom">
        <div class="wrap">
            <ul class="parent">
                <li>
                    <span>Требования к заёмщикам:</span>
                    <ul class="child">
                        <li>возраст от 21 года до 65 лет</li>
                        <li>регистрация: Российская Федерация</li>
                        <li>местонахождение: Москва, Московская обл.; Санкт-Петербург, Ленинградская обл</li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>

<?php endwhile; // End of the loop. ?>
<?php
get_footer();
