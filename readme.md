[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/carono/php-commerceml/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/carono/php-commerceml/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/carono/php-commerceml/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/carono/php-commerceml/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/carono/php-commerceml/badges/build.png?b=master)](https://scrutinizer-ci.com/g/carono/php-commerceml/build-status/master)
[![Latest Stable Version](https://poser.pugx.org/carono/commerceml/v/stable)](https://packagist.org/packages/carono/commerceml)
[![Total Downloads](https://poser.pugx.org/carono/commerceml/downloads)](https://packagist.org/packages/carono/commerceml)
[![License](https://poser.pugx.org/carono/commerceml/license)](https://packagist.org/packages/carono/commerceml)

# PHP CommerceML

Бібліотека для універсального парсингу файлів [CommerceML2](http://v8.1c.ru/edi/edi_stnd/90/92.htm).

> [!NOTE]
> Цей репозиторій є форком оригінального проєкту [alex8bits/php-commerceml](https://github.com/alex8bits/php-commerceml) (який своєю чергою базується на [carono/php-commerceml](https://github.com/carono/php-commerceml)).

# Встановлення
`composer require imrev-agency/commerceml`

# Каталог та товари

```php
// $filePath - повний шлях до XML файлу import.xml або його контент
$cml = new CommerceML();
$cml->loadImportXml('/fullpath/import.xml'); // Завантажуємо товари
$cml->loadOffersXml('/fullpath/offers.xml'); // Завантажуємо пропозиції
```

# Робота з товарами та пропозиціями

```php
foreach ($cml->catalog->products as $product){
    echo $product->name; // Виводимо назву товару (Товари->Товар->Найменування)
    foreach ($product->offers as $offer){
        echo $offer->name; // Виводимо назву пропозиції (Пропозиції->Пропозиція->Найменування)
        echo $offer->prices[0]->cost; // Виводимо першу ціну пропозиції (Пропозиції->Пропозиція->Ціни->Ціна->ЦінаЗаОдиницю)
    }
}
```

## \Zenwalker\CommerceML\CommerceML  

|Метод|XML|Опис|
|-----|----|--------|
|catalog|Каталог|Об'єкт каталогу|
|classifier|Класифікатор|Об'єкт класифікатора|
|offerPackage|ПакетПропозицій|Об'єкт пропозицій|

## \Zenwalker\CommerceML\Model\OfferPackage

|Метод|XML|Опис|
|-----|----|--------|
|offers|Пропозиції->Пропозиція|Список всіх пропозицій|
|priceTypes|ТипиЦін->ТипЦіни|Список всіх типів цін|

## \Zenwalker\CommerceML\Model\Product

|Метод|XML|Опис|
|-----|----|--------|
|properties|Каталог->Товари->Товар->ЗначенняВластивостей|Властивості продукту, `$product->properties[0]->value`|
|requisites|Каталог->Товари->Товар->ЗначенняРеквізитів->ЗначенняРеквізиту|Реквізити продукту, `$product->requisites[0]->value`|
|offers|Пропозиції->Пропозиція|Список пропозицій для продукту|
|group|Каталог->Товари->Товар->Групи=>Класифікатор->групи->група|Група товару `$product->group->name` |
|images|Каталог->Товари->Товар->Зображення|Список зображень товару|

## \Zenwalker\CommerceML\Model\Offer

|Метод|XML|Опис|
|-----|----|--------|
|prices|Пропозиції->Пропозиція->Ціни->Ціна|Всі ціни пропозиції|
|specifications|Пропозиції->Пропозиція->ХарактеристикиТовару->ХарактеристикаТовару|Список всіх характеристик пропозиції|