# Customization

## Changing the background image

In `Website/images/Refresh.php`, search for `body { ... }`. Replace `background-color: #ccc;` with `background: url(...) #fff top left no-repeat`, for example.

## Removing the pre-defined accounts

You should not remove these accounts, neither `Demo` nor `Admin`. Both are required for the game. However, as mentioned in the setup guide, you should change the password for the `Admin` user immediately after setting up the game.

## Renaming leagues/countries

In order to rename country `<COUNTRY_OLD>` to country `<COUNTRY_NEW>` with ISO 3166-1 alpha-2 code `<ISO_ALPHA_2_NEW>`, execute the following SQL statements on the database:

```
UPDATE man_ligen SET name = '<COUNTRY_NEW> 1', land = '<COUNTRY_NEW>', isoAlpha2 = '<ISO_ALPHA_2_NEW>' WHERE name = '<COUNTRY_OLD> 1';
UPDATE man_ligen SET name = '<COUNTRY_NEW> 2', land = '<COUNTRY_NEW>', isoAlpha2 = '<ISO_ALPHA_2_NEW>' WHERE name = '<COUNTRY_OLD> 2';
UPDATE man_ligen SET name = '<COUNTRY_NEW> 3', land = '<COUNTRY_NEW>', isoAlpha2 = '<ISO_ALPHA_2_NEW>' WHERE name = '<COUNTRY_OLD> 3';
UPDATE man_ligen SET name = '<COUNTRY_NEW> 4', land = '<COUNTRY_NEW>', isoAlpha2 = '<ISO_ALPHA_2_NEW>' WHERE name = '<COUNTRY_OLD> 4';
UPDATE man_vNamePool SET land = '<COUNTRY_NEW>' WHERE land = '<COUNTRY_OLD>';
```
