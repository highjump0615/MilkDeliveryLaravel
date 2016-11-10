/*!
 * Distpicker v1.0.2
 * https://github.com/tshi0912/city-picker
 *
 * Copyright (c) 2014-2016 Tao Shi
 * Released under the MIT license
 *
 * Date: 2016-02-29T12:11:36.473Z
 */

(function (factory) {
    if (typeof define === 'function' && define.amd) {
        // AMD. Register as anonymous module.
        define('ChineseDistricts', [], factory);
    } else {
        // Browser globals.
        factory();
    }
})(function () {
    var ChineseDistricts = {
            86: {
                10: '北京',
                11: '河北',
            },
    
            // 86: {
            //     'A-G': [
            //         {code: '10', address: '北京市'},
            //         ],
            //     'H-K': [
            //         {code: '11', address: '河北省'},
            //         ],
            // },
            10: {
                100: '北京市',
            },
            100: {
                1000: '通州区',
            },

            1000: {
                10001: '中创大姐'
            },
            10001:{
                100011: '运河明珠'
            },

            11: {
                110: '天津市'
            },
            110: {
                1101: '和平区',
            },
            1101: {
                11011: '长安区',
            },
            11011: {
                110111: '收費的大街',

            },
            110111: {
                1101111: '樓上的看法',
            },
        }
        ;

    if (typeof window !== 'undefined') {
        window.ChineseDistricts = ChineseDistricts;
    }

    return ChineseDistricts;

});
