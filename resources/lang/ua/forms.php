<?php

return [
    'fields'   => [
        'login'               => [
            'email_or_name' => 'Логин или электронная почта',
            'email'         => 'E-mail',
            'password'      => 'Пароль',
            'remember'      => 'Запомнить меня',
        ],
        'reset_password'      => [
            'email'    => 'E-mail',
            'password' => 'Пароль',
            'token'    => 'Код доступа',
        ],
        'profile'             => [
            'password'              => 'Пароль',
            'password_confirmation' => 'Повтор пароля',
            'email'                 => 'Электронная почта',
            'login'                 => 'Логин',
            'name'                  => 'Имя',
            'surname'               => 'Фамилия',
            'phone'                 => 'Телефон',
            'company'               => 'Компания',
            'subscription'          => 'Подписаться на рассылку',
            'file'                  => 'Выбрать файл',
        ],
        'buy_one_click'       => [
            'name'     => 'Ваше имя',
            'phone'    => 'Ваш номер телефона',
            'quantity' => 'Необходимое количество',
            'comments' => 'Ваше сообщение',
        ],
        'submit_application'  => [
            'name'               => 'Ваше имя',
            'phone'              => 'Номер телефона',
            'email'              => 'E-mail для отправки расчета',
            'comments'           => 'Ваше сообщение',
            'quantity'           => 'Кол-во',
            'attach_file_text_1' => 'Прикрепить файл',
            'attach_file_text_2' => 'Нажмите или просто перетащиете файл сметы в окно.',
        ],
        'notify_when_appears' => [
            'name'  => 'Ваше имя',
            'email' => 'Электронная почта',
        ],
        'checkout'            => [
            'full_name'                      => 'ФИО',
            'name'                           => 'Ім\'я',
            'surname'                        => 'Прізвище',
            'patronymic_name'                => 'Отчество',
            'phone'                          => 'Номер телефону',
            'email'                          => 'Электронная почта',
            'payment_method_cash'            => 'Оплата готівкою',
            'payment_method_by_card_courier' => 'Картою кур\'єру',
            'payment_method_card'            => 'Картою онлайн',
            'delivery_method_pickup'         => 'Самовивіз',
            'delivery_method_delivery'       => 'Доставка',
            'delivery_method_np_courier'     => 'Курьер "Нова пошта"',
            'delivery_method_np_branch'      => 'Отделение "Нова пошта"',
            'delivery_method_ukr_branch'     => 'Отделение "Укрпошта"',
            'delivery_city'                  => 'Город',
            'delivery_pharmacy'              => 'Аптека',
            'delivery_pharmacy_2'            => 'Аптека для доставки',
            'delivery_street'                => 'Вулиця / Проспект',
            'delivery_house'                 => 'Буд.',
            'delivery_house_full'            => 'Будинок',
            'delivery_entrance'              => 'Під.',
            'delivery_floor'                 => 'Поверх',
            'delivery_flat'                  => 'Квартира',
            'date_time'                      => 'Дата і час доставки',
            'delivery_code'                  => 'Индекс',
            'comments'                       => 'Коментар',
            'agree'                          => 'Соглашаюсь с условиями <a class="link-agree" href=":link" target="_blank">пользовательского соглашения</a>',
            'agree_2'                        => 'Соглашаюсь с условиями пользовательского соглашения',
            'payment_method_company_name'    => 'Название компании',
            'payment_method_erdpo'           => 'ЕДРПО',
            'surrender'                      => 'Решта з',
            'person'                         => 'Кіл-ть персон',
            'agreement'                      => 'Даю добровільну згоду на обробку моїх персональних даних',
            'call_me_back'                   => 'Передзвонити мені після оформлення замовлення',
            'title'                          => 'Контактна',
            'sub_title'                      => 'Інформація',
            'certificate'                    => 'Cертифікат',
            'birthday'                       => 'У мене День народження - <i>12</i>%',
        ],
        'search'              => [
            'string' => 'Искать...',
        ],
        'wish_list'           => [
            'name'                   => 'Название списка',
            'add_comment_to_product' => 'Добавить кометарий видный только мне'
        ],
        'captcha'             => 'reCAPTCHA'
    ],
    'buttons'  => [
        'login'               => [
            'submit' => 'Вход'
        ],
        'register'            => [
            'submit' => 'Создать нового пользователя'
        ],
        'forgot_email'        => [
            'submit' => 'Отправить'
        ],
        'reset_password'      => [
            'submit' => 'Сохранить'
        ],
        'notify_when_appears' => [
            'open'   => 'Уведомить о наличии',
            'submit' => 'Отправить'
        ],
        'submit_application'  => [
            'submit' => 'Заказать'
        ],
        'buy'                 => [
            'order'              => 'Заказать',
            'in_basket'          => 'В корзину',
            'submit'             => 'Купить',
            'put_in_basket'      => 'Положить в корзину',
            'now'                => 'Купить сейчас',
            'one_click'          => 'купити<br> в 1 клік',
            'order_is_confirmed' => 'Заказ подтверждаю',
            'back_to_basket'     => 'Вернуться в корзину',
            'payment'            => 'Расчет',
            'view_availability'  => 'Посмотреть',
        ],
        'comment'             => [
            'add_comment'       => 'Добавить комментарий',
            'add_review'        => 'Добавить отзыв',
            'add_first_comment' => 'Добавить первый комментарий',
            'add_first_review'  => 'Добавить первый отзыв',
            'submit_comment'    => 'Отправить комментарий',
            'submit_review'     => 'Отправить отзыв',
            'submit_reply'      => 'Оставить ответ',
            'reply_comment'     => 'Ответить на комментарий',
            'reply_review'      => 'Ответить на отзыв',
            'all_comments'      => 'Все комментарии',
            'look_at_page'      => 'Посмотреть на странице',
            'look_at_page_2'    => 'Посмотреть',
        ],
        'feedback'            => [
            'submit' => 'Отправить',
        ],
        'calculation_form'    => [
            'submit' => 'Отправить',
        ],
        'search'              => [
            'submit' => 'Найти',
        ],
        'profile'             => [
            'submit' => 'Сохранить',
        ],
        'wish_list'           => [
            'add_new_list'     => 'Новый список',
            'remove_list'      => 'Удалить',
            'rename_list'      => 'Переименовать',
            'submit'           => 'Добавить',
            'add_to_compare'   => 'Добавить в сравнение',
            'remove_from_list' => 'Удалить',
            'to_favorites'     => 'В избранное',
        ],
        'checkout'            => [
            'to_issue'          => 'Оформлення замовлення',
            'continue_shopping' => 'Продолжить покупки',
            'clear_basket'      => 'Очистить корзину',
            'use'               => 'Застосувати',
            'clear'             => 'Очистити',
            'cancel'            => 'Скасувати', 
        ]
    ],
    'labels'   => [
        'register' => [
            'personal_information' => 'Персональная информация'
        ],
        'comment'  => [
            'rate_stars'    => 'Оценка',
            'user_name'     => 'Имя',
            'user_email'    => 'Электронная почта',
            'text_comment'  => 'Комментарий',
            'text_review'   => 'Отзыв',
            'reply'         => 'Ответ',
            'advantages'    => 'Достоинства',
            'disadvantages' => 'Недостатки',
            'notify'        => 'Сообщить об ответе по эл. почте'
        ],
        'checkout' => [
            'recipient'       => 'Получатель',
            'payment_method'  => 'Спосіб оплати',
            'delivery_method' => 'Спосіб доставки',
            'datepicker'      => 'Час доставки',
            'total_amount'    => 'Итого на сумму',
            'total_amount_2'  => ':product на сумму :amount',
            'total_amount_3'  => 'Сума до оплати: :amount',
            'total_amount_4'  => 'Сумма',
            'delivery_amount' => 'Вартість доставки: :amount грн. Безкоштовна доставка при замовленні від :min грн',
            'delivery_pickup' => 'Ви заощаджуєте :amount',
            'delivery_free'   => 'Доставка безкоштовно',
            'you_saved'       => 'Ви заощаджуєте :amount',
        ],
        'search'   => [
            'example'        => 'Пример:',
            'example_string' => 'Провод ПВС 3х1,5',
        ],
        'profile'  => [
            'additional_data' => 'Дополнительные данные',
            'change_password' => 'Изменить пароль',
        ],
        'pharmacy' => [
            'address'       => 'Адрес',
            'phones'        => 'Телефоны',
            'working_hours' => 'Время работы',
        ]
    ],
    'titles'   => [
        'comment' => [
            'new_comment'          => 'Добавить комментарий',
            'new_review'           => 'Добавить отзыв',
            'new_first_comment'    => 'Добавить первый комментарий',
            'new_first_review'     => 'Добавить первый отзыв',
            'new_reply_to_comment' => 'Добавить ответ к комментарию',
            'new_reply_to_review'  => 'Добавить ответ к отзыву',
        ],
        'buy'     => [
            'one_click'           => 'Купить сейчас',
            'notify_when_appears' => 'Сообщить о наличии',
            'order_product'       => 'Заказ товара',
        ]
    ],
    'values'   => [
        'comment'   => [
            'rate_1' => 'Плохо',
            'rate_2' => 'Так себе',
            'rate_3' => 'Нормально',
            'rate_4' => 'Хорошо',
            'rate_5' => 'Отлично',
        ],
        'checkout'  => [
            'np_choice_city'     => 'Выбрать город',
            'np_choice_pharmacy' => 'Выбрать аптеку',
        ],
        'yes'       => 'Да',
        'no'        => 'Нет',
        'value_not' => 'Значение не задано',
    ],
    'helps'    => [
        'forgot_email' => [
            'output_1' => 'Для продолжения введите адрес электронной почты, который Вы указали при регистрации.',
        ],
        'comment'      => [
            'user_name' => 'Если оставить пустым, то Вы будете указаны как "Гость"',
        ],
        'checkout'     => [
            'delivery_method_pickup'     => '(Бесплатно)',
            'delivery_method_delivery'   => '',
            'delivery_method_np_courier' => '',
            'delivery_method_np_branch'  => '',
            'delivery_method_ukr_branch' => '',
            'attention_1'                => '',
            'attention_2'                => '',
        ]
    ],
    'messages' => [
        'login'               => [
            'account_locked'        => 'Учетная запись пользователя заблокирована администратором.',
            'account_not_activated' => 'Электронная почта учетной записи не подтверждена.',
            'failed'                => 'Эти данные не соответсвуют указанным при регистрации.',
            'throttle'              => 'Слишком много попыток входа. Повторите попытку через :seconds секунды.',
        ],
        'register'            => [
            'confirm_registration' => 'На адрес, который Вы указали при регистрации, было отправлено письмо для подтверждения электронной почты.'
        ],
        'verify_email'        => [
            'authenticate' => 'Для подтверждения почты Вы должны быть залогинены.<br>Войдите на сайт под свои логином и паролем и перейдите по ссылке из письма.',
            'resent'       => 'На адрес, который Вы указали в настройка аккаунта, было повторно отправлено письмо для подтверждения электронной почты.',
        ],
        'reset_password'      => [
            'message' => 'На адрес, который Вы указали в настройка аккаунта, было отправлено письмо для сброса пароля.',
        ],
        'profile'             => [
            'message' => 'Данные профиля сохранены.',
        ],
        'wish_list'           => [
            'name_exists'                => 'Такое название спика уже существует.',
            'add_product_in_new_list'    => 'Товар добавлен в новый список желаний.',
            'add_product_in_list'        => 'Товар добавлен в список желаний.',
            'product_is_already_on_list' => 'Товар уже добавлен в этот список.',
        ],
        'notify_when_appears' => [
            'thanks_notification' => '<p>Спасибо! Мы Вам сообщим когда данный товар появится в наличии.</p>'
        ],
        'buy_one_click'       => [
            'thanks' => '<p>Спасибо! Наш менеджер свяжется с Вами в ближайшее время.</p>'
        ],
        'checkout'            => [
            'failure_payment' => 'Оплата карткою не пройшла'
        ]
    ]
];
