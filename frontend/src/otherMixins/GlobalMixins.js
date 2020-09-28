export default {
    methods : {
        //method = get, put, patch or any HTML request
        //url = api link
        //obj = model targeted of this function
        //responsePath = json path to target specific key and value
        _getData(method,url,filter,obj,responsePath = []){
            let resData;
            axios[method](url+this.generateFilterURL(filter))
            .then(response=>{
                resData = response
            })
            .catch(error=>{
                resData = error
            })
            .finally(()=>{
                // console.log(resData)
                if (responsePath.length == 0) return this[obj] = resData;
                return this[obj] = responsePath.reduce((o, n) => o[n], resData);
            })
        },
        _checkDuplicateInObject(propertyName, inputArray) {
            var seenDuplicate = false,
                testObject = {};

            inputArray.map(function(item) {
                let itemPropertyName = item[propertyName];   
                if (itemPropertyName in testObject) {
                    testObject[itemPropertyName].duplicate = true;
                    item.duplicate = true;
                    seenDuplicate = true;
                }
                else {
                    testObject[itemPropertyName] = item;
                    delete item.duplicate;
                }
            });
            return seenDuplicate;
        },
        _removeDuplicates(arrayAffected, prop) {
            return arrayAffected.filter((obj, pos, arr) => {
                return arr.map(mapObj => mapObj[prop]).indexOf(obj[prop]) === pos;
            });
        },
    }
}