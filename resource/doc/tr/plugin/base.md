# Temel Eklentiler

Temel eklentiler genellikle genel bileşenlerdir ve genellikle composer kullanılarak kurulur ve kodları vendor klasörüne yerleştirilir. Kurulum sırasında, bazı özelleştirilmiş yapılandırmaların (middleware, işlem, rota vb. yapılandırmalar) otomatik olarak `{ana proje}config/plugin` dizinine kopyalanmasına izin verilir. Webman bu dizini yapılandırma olarak tanır ve bu yapılandırmaları ana yapılandırmaya birleştirerek eklentilerin webman'ın herhangi bir yaşam döngüsüne dahil olmasını sağlar.

Daha fazla bilgi için [Temel Eklenti Oluşturma](create.md) konusuna bakın.
