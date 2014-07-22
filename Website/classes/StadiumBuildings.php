<?php

class StadiumBuildings {

    /**
     * Returns a list of stadium building types
     *
     * [0] = short name (ID)
     * [1] = full name
     * [2] = one building of this type is necessary per this number of spectators
     * [3] = cost per single building of this type
     *
     * @return array list of building types
     */
    public static function getList() {
        return array(
            array('parkplatz', 'Parkplatz', 7500, 30000),
            array('ubahn', 'U-Bahn', 40000, 90000),
            array('restaurant', 'Restaurant', 15000, 320000),
            array('bierzelt', 'Bierzelt', 20000, 74000),
            array('pizzeria', 'Pizzeria', 12000, 90000),
            array('imbissstand', 'Imbissstand', 10000, 45000),
            array('vereinsmuseum', 'Vereinsmuseum', 50000, 655000),
            array('fanshop', 'Fanshop', 30000, 160000),
        );
    }

}

?>
