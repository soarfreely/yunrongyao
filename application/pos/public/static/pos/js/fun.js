function focus(id)
{
    var idStr = '';
    if(id){
        idStr = '#' + id;
        $(idStr).focus();
    }
}
