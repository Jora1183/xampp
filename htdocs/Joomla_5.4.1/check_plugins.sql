SELECT 
    extension_id, 
    name, 
    enabled,
    folder,
    element
FROM jos_extensions 
WHERE folder = 'solidrespayment'
ORDER BY element;
