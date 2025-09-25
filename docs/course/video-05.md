
# Enabling OData

In this video, we’ll connect your UI to real data using OData. You’ll see why UI5 was built to talk to structured APIs, how the Lodata package brings OData v4 into Laravel, and how easy it is to expose your `User` model as a discoverable OData endpoint. By the end, Laravel will be speaking UI5’s native data language.

<Youtube id="aEM8WkNnCDw" />

## References

* [OASIS OData standard](https://www.oasis-open.org/committees/odata/){target="_blank"}
* [OpenUI5 Model docs](https://github.com/UI5/docs/blob/main/docs/04_Essentials/models-d2c8cf7.md){target="_blank"}
* [Lodata.io](https://lodata.io){target="_blank"}

## Quick Reference

These are the commands and snippets from this video. For the why and the bigger picture, be sure to follow along in the video above.

**1. Extending Endpoint**

Open `ui5/Users/src/UsersApp.php` and make the class extend `Flat3\Lodata\Endpoint`.

Then add the parent call to the constructor

```php
parent::__construct($module->getSlug());
```

and the `discover` method

```php
public function discover(Model $model): Model
{
    return $model->discover(User::class);
}
```

## Code

To get the exact code with all edits in this video applied, run

```bash
git checkout episode-04
```

**2. Test in Browser**

In your browser open [http://127.0.0.1:8000/odata/users/$metadata](http://127.0.0.1:8000/odata/users/$metadata){target="_blank"}

## Code

To get the exact code with all edits in this video applied, run

```bash
git checkout episode-05
```
