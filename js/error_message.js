function displayErrorMessage(parentId, errorText, childId=null){
    var parentNode = document.getElementById(parentNode);
    if (!parentnode){
        window.alert(errorText);
        return ;
    }
    var errorNode = document.createElement('div');
    errorNode.setAttribute('id', childId);
    parentNode.append(errorNode);
    return ;
}
