module.exports = class {
    static getExt(str, del = '.') {
        let s = str.split(del);
        let rs = s.reverse();
        return rs[0];
    }
};